<?php

use App\Domains\Profile\Models\UserProfile;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
  /**
   * Honorifics were stored as a mix of labels ("Mr.") and bare keys ("Mr"),
   * plus typos like "Miss.", so the select never matched the stored value.
   */
  public function up(): void
  {
    DB::table('user_profiles')
      ->whereNotNull('honorific')
      ->where('honorific', '<>', '')
      ->select('id', 'honorific')
      ->orderBy('id')
      ->chunk(500, function ($rows) {
        foreach ($rows as $row) {
          $normalized = UserProfile::normalizeHonorific($row->honorific);

          if ($normalized !== $row->honorific) {
            DB::table('user_profiles')->where('id', $row->id)->update(['honorific' => $normalized]);
          }
        }
      });
  }

  public function down(): void
  {
    // Normalisation is not reversible — the original spellings were noise.
  }
};
