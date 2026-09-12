<?php

use App\Domains\Profile\Models\UserProfileType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Interests moves from being a per-profile-type attribute (STUDENT and
 * EXTERNAL 'interests', ACADEMIC_STAFF 'research_interests') to a single
 * 'interests' column on user_profiles. Backfills using the same priority
 * the old UserProfile::getInterestsAttribute() accessor used, then strips
 * the now-redundant keys out of user_profile_types.attributes.
 */
return new class extends Migration {
  public function up(): void
  {
    Schema::table('user_profiles', function (Blueprint $table) {
      $table->json('interests')->nullable()->after('current_position');
    });

    $typesByProfile = DB::table('user_profile_types')
      ->select('id', 'user_profile_id', 'type', 'attributes')
      ->get()
      ->groupBy('user_profile_id');

    foreach ($typesByProfile as $profileId => $types) {
      $byType = $types->keyBy('type');

      $sources = [
        UserProfileType::TYPE_ACADEMIC_STAFF => 'research_interests',
        UserProfileType::TYPE_EXTERNAL => 'interests',
        UserProfileType::TYPE_STUDENT => 'interests',
      ];

      foreach ($sources as $type => $key) {
        $attributes = json_decode($byType->get($type)?->attributes ?? 'null', true) ?? [];

        if (! empty($attributes[$key])) {
          DB::table('user_profiles')
            ->where('id', $profileId)
            ->update(['interests' => json_encode(array_values($attributes[$key]))]);
          break;
        }
      }
    }

    foreach (DB::table('user_profile_types')->get() as $row) {
      $attributes = json_decode($row->attributes ?? 'null', true) ?? [];

      if (array_key_exists('interests', $attributes) || array_key_exists('research_interests', $attributes)) {
        unset($attributes['interests'], $attributes['research_interests']);
        DB::table('user_profile_types')->where('id', $row->id)->update(['attributes' => json_encode($attributes)]);
      }
    }
  }

  /**
   * Reverses only the schema change; the per-type interest keys stripped
   * above are not restored.
   */
  public function down(): void
  {
    Schema::table('user_profiles', function (Blueprint $table) {
      $table->dropColumn('interests');
    });
  }
};
