<?php

namespace Database\Seeders;

use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Seeder;
use App\Domains\News\Models\News;

class NewsSeeder extends Seeder
{
    use DisableForeignKeys, TruncateTable;
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->disableForeignKeys();
        $this->truncateMultiple([
            'news',
        ]);

        $news = [
            [
                "title" => "Third year undergraduate in the award winning team working on Cloud Atlas",
                "description" => "<p>Three third year undergraduates from the University of Peradeniya have invented a hundred percent Bio-Degradable Sanitizer bottle and Sanitary wipe package to combat this issue. Navodya Kumari Ekanayake, a third-year medical student; Shakthi Senarathne, a third-year medical student; Denuka Jayaweera, a third-year engineering undergraduate from the Department of Computer Engineering are working on this team CLOUD ATLAS.</p><p>Their aims are to make an impact on environmental conservation, to contribute towards United Nations Sustainable development goals three six nine and twelve, to give a sustainable alternative for sanitary needs in the market, to reduce the usage of polythene and plastics and to take a value out of banana stem which is thrown away after harvesting.</p><p>Plastic usage has been abused and adulterated by mankind, stepping towards the highest risks of pollution. The net increment of usage hasn’t been calculated for the year 2020, but undoubtedly an exponential rise can be predicted. Disposal and the 3R method seems to be in the air, but plastic landfill is the commonest problem well known, but solutions are not yet properly addressed by any responsible authority.</p><p>Due to the covid pandemic, the usage of sanitiser bottles increased drastically. If five million [which is less than twenty-five percent of the population] people discard one sanitiser bottle each per two weeks, the number of sanitiser bottles discarded per month is approximately ten million bottles. If so the total amount of plastic landfill added by sanitiser bottles per month equals the total number of bottles discarded into the mean weight of one bottle which eventually add up to around three hundred thousand kilograms of plastics per month only by sanitiser bottles.</p><p>They figured out this directly affected the usage of polythene and plastic in Sri Lanka. And to add to that normal bottle of sanitiser takes about four hundred and fifty to six hundred years to get decomposed, other than that there was no such biodegradable product in the market similar to this not only in Sri Lanka but in the global context as well.</p><p>The prototyped product was the first bottle model and sanitary wipe in the world, to be made out of a banana stem as the main ingredient. All these products have a net zero percent environmental impact, Which decomposes completely within one month. They chose banana paper over normal recycled paper, to address this environmental impact issue. Even recycled paper demands an additional cost of cutting trees, which is harmful to the prevailing situation of the country. They have aimed at minimal deforestation and carbon footprint in the production process.</p><p>Recently, bamboo came as a substitute for normal trees in China and East Asian countries. But in countries like Sri Lanka, bamboo cannot be used as a substitute ingredient for paper, as we don’t have considerable bamboo cultivation targeting production. Bamboo is highly resilient against soil erosion, especially in river beds. Banana has the same properties found in bamboo and is easily found in Sri Lanka. Add to that the fineness of a banana is better than bamboo (with an average fineness of two thousand four hundred Nano meters). Worldwide there is a good market for bamboo wipes due to their softness. Therefore banana would be better</p><p>The team emerged as the ideation champions in Thinkwave 2.0 organized by the University of Moratuwa, Overall runners up in Thinkwave 2.0, champions of Pera Inventra 2021 and second runners up of YES Youth Entrepreneurship Summit organized by the University of Sri Jayawaradenapura.</p><p>Their work has also being featured in local newspaper.</p>",
                "url" => "third-year-undergraduate-in-the-award-winning-team.html",
                "image" => "1724778421.jpg",
                "link_url" => null,
                "link_caption" => null,
                "enabled" => 1,
                "created_by" => 1,
                "published_at" => "2021-06-02"
            ],
            [
                "title" => "Prof. Roshan Ragel among the top ranking scientists",
                "description" => "<p><span style=\"color=> rgb(33, 37, 41);\">We warmly congratulate Prof. Roshan Ragel from the Department of Computer Engineering, for being ranked among the top scientists in the field of Engineering and Technology\/Computer Science as per the AD scientific Index 2021. Further details are at&nbsp;<\/span><a href=\"https=>\/\/www.adscientificindex.com\/\" target=\"_blank\" style=\"color=> rgb(13, 110, 253); background-color=> rgb(255, 255, 255);\">https=>\/\/www.adscientificindex.com\/<\/a><\/p>",
                "url" => "prof-roshan-ragel-among-the-top-ranked-scientists",
                "image" => "1724778381.png",
                "link_url" => null,
                "link_caption" => null,
                "enabled" => 1,
                "created_by" => 1,
                "published_at" => "2021-07-01"
            ],
            [
                "title" => "The annual general meeting of the Association of Computer Engineering Students",
                "description" => "<p>The annual general meeting of the Association of Computer Engineering Students (ACES) was successfully held on 28th October 2021 with the participation of students and lecturers of the department of Computer Engineering, University of Peradeniya. The outgoing president, Mr. Malitha Liyanage commenced the meeting by welcoming all the members and sharing a few memories of the events conducted during the past year. After presenting the meeting minutes of the last annual general meeting and the budget report for the last year by the secretary, Mr. Hashan Eranga, and the junior treasurer, Mr. Nipun Dewanarayane, the new office bearers of ACES for the term 2021/22 were appointed.</p><p>Accordingly, Mr. Randika Viraj was appointed as the President for the new term. Next up was the appointment for the role of Vice President and Mr. Thushara Weerasundara was appointed for it. Subsequently, Mr. Imesh Balasuriya and Mr. Ridma Jayasundara were appointed for the roles of Secretary and Assistant Secretary respectively. Afterwards, Dr. Isuru Nawinne, a Senior Lecturer of the department, was assigned as the Senior Treasurer followed by the appointment of Mr. Nadeesha Diwakara as the Junior Treasurer. Then, Miss Nanduni Gamage and Miss Isara Tillekaratne were appointed for the roles of Editor and Junior Editor respectively. Finally, eight committee members were assigned representing the batches E16, E17, and E18 as follows; Mr. Kavindu Hewamanage, Mr. Deshan Liyanaarachchi, and Mr. Heshan Dissanayake, representing the E16 batch, Mr. Pubudu Bandara and Mr. Adithya Gallage representing the E17 batch and Mr. Ishta Jayakody, Miss Roshila Sewwandi and Mr. Ruchira Tharaka, representing the E18 batch.</p><p>After appointing the new members, Mr. Randika, the newly appointed president took a few moments to thank the past committee for their hard work. Also, he further added a brief introduction to the upcoming plans and asked for any opinions and suggestions regarding this endeavor. The latter part of the meeting comprised a discussion among the staff members and the students on the suggestions for the upcoming year. Prof. Roshan Ragel expressed his idea that the pandemic situation has brought forth the need for online events and that ACES could be at the forefront of organizing such events with novel concepts. It was highlighted that having two plans for the online mode as well as the physical mode could be beneficial to overcome the uncertainties and the AGM marked the conclusion with a bunch of experience-based advice and great congratulations to the new committee for the upcoming year.</p><p>ACES is looking forward to bring more creative and productive programs in the coming year and invites everyone to stay tuned.</p><p>Reported by Nanduni Gamage</p>",
                "url" => "the-annual-general-meeting-of-the-association-of-computer-engineering-students",
                "image" => "1724778359.jpg",
                "link_url" => null,
                "link_caption" => null,
                "enabled" => 1,
                "created_by" => 1,
                "published_at" => "2021-11-21"
            ],
            [
                "title" => "Team IT crowd wins the championship in Yes Hub Bootcamp’21",
                "description" => "<p>The Team IT Crowd consisted of Sandun Kodagoda,Adithya Gallage and Denuka Jayaweera, undergraduates of Department computer Engineering became champions in the YES hub boot camp ’21 organized by the Yes Hub, Venture Frontier along with the US Embassy.</p><p>This Entrepreneurial , Innovation and Pitching event had around 50 teams participating in the event after a preliminary qualification held offline. The competitors participated in the competition ranged from MSc Holders to Undergraduates. after the first round the team IT Crowd got selected to Top Six and after the second round they eventually went on to win the completion. The Event was graced by more than 15 national and international entrepreneurs businessmen who appreciated the effort thrown in by the team.</p><p>The Idea Pitched by the Team IT crowd started as their second year project in the university which is about developing the concept of smart lockers.</p>",
                "url" => "team-it-crowd-wins-the-championship",
                "image" => "1724778320.jpg",
                "link_url" => null,
                "link_caption" => null,
                "enabled" => 1,
                "created_by" => 1,
                "published_at" => "2021-12-02"
            ],
            [
                "title" => "Department Staff meets the Vice Chancellor on International Collaborations",
                "description" => "<p>Professor MD. Lamawansa, The Vice Chancellor of University of Peradeniya met with a group of academics from the Faculty of Engineering to discuss strengthening international collaborations. The group comprised of Dr. Udaya Dissanayake, the Dean, Faculty of Engineering, Prof. Roshan Ragel, Dr. Kamalanath Samarakoon, the Head of the Department of Computer Engineering, Director/ETIC, Prof. CNRA. Alles, Director of International Research Office, Dr. Panduka Neluwala, Coordinator, International Relations of the Faculty of Engineering, Dr. Damayanthi Herath, Coordinator, International Relations of the Department of Computer of Engineering.</p><p>Dr. Samarakoon shared insights covering expanding University teaching and learning activities, incubation, and collaborations between the faculties of the University with industry, and foreign universities with regard to product development with the experience gained from his recent visits to international universities.</p><p>During this meeting, the Vice-Chancellor signed three MoUs to foster further collaborations between the University of Peradeniya and the Indian Institute of Technology Delhi (IITD), Indian Institute of Technology Roorkee (IITR), and Lovely Professional University (LPU) of India.</p>",
                "url" => "department-staff-meets-the-vice-chancellor-on-international-collaborations",
                "image" => "1724778089.png",
                "link_url" => null,
                "link_caption" => null,
                "enabled" => 1,
                "created_by" => 1,
                "published_at" => "2023-01-02"
            ],
            [
                "title" => "ACES ties up with Nenathambara to make its largest coding competition on the island to the next level",
                "description" => "<p>The Department of Computer Engineering of the University of Peradeniya has developed over time to the point where it is today, delivering one of the most esteemed Computer Engineering degree programmes in Sri Lanka. The Association of Computer Engineering Students (ACES), the official student organization of the Department, guides undergraduate students through many initiatives, such as the ACES Hackathon, ACES Coders, Spark, and Nenathambara, which help them build their soft skills while bridging the gap between academia, industry, and schools.</p><p>In the series of achievements, the day to witness the significant milestones of two remarkable programmes, ACES Coders v9.0, the island’s largest competitive programming challenge, and the distribution ceremony of Arduino kits for the worthwhile Project Nenathambara, arrived on the 17th of December 2022.</p><p>After giving a brief introduction about the rules and regulations of the competition, competitors headed to their respective desks in Drawing Office 1 of the University of Peradeniya for their 12-hour coding mission. The competitors were expected to use their logical thinking and programming skills to come up with solutions to the competition’s realistic problems, which were conducted through the Hackerank platform. With the refreshments offered, all the teams worked all night long on coding energetically with a single goal in mind. However, the scoreboard rapidly changed, showing how extremely competitive the teams were. Moreover, it enhances the participants’ ability to work in a team and manage their time well because they must compete in the competition as a team within the allotted time frame.</p><p>Finally, the ACES Coders v9.0 winners were announced and received cash prizes, and other participants who took part received certificates recognising their participation. After a 12-hour sleepless coding marathon and a lot of effort, Team BitFlippers from the University of Peradeniya took first place out of over 100 teams. Team BitLasagna from the University of Peradeniya were the runners-up, while Team DragonCoders from the University of Moratuwa became the second runners-up in this competition.</p><p>Mr Ruchika Perera, Secretary of the Hackers’ Club at the University of Peradeniya, gave the closing remarks to the event, thanking all the helping hands behind the achievement. Giving school children exposure to the world of coding helps them to broaden and realign their perspective, and recognising events like ACES Coders can undoubtedly encourage young souls to explore the limitless opportunities of university life.</p><p>The event was indeed a tremendous success, and all of this was made possible by the support provided in the form of sponsorship. This year, the competition was sponsored by Synopsys Sri Lanka (Platinum sponsor), Bitzify (Gold sponsor), GTN Technologies (Event Partner), Avtra (Silver sponsor), Clouda Inc. (Startup Partner), The Software Practice, Acentura, and Zincat (Supporting Partners), and Arteculate and Gauge (Media Partners). Speaking on behalf of the sponsors, Mr V. Kathircamalan from Synopsys addressed the audience, and Mr Farazy Fahmy, the director of research and development at Synopsys, mentioned the value of education and how responsible we should be to take care of the people who paid for it. Furthermore, souvenirs were given out to appreciate the sponsors for their enormous support.</p>",
                "url" => "aces-ties-up-with-nenathambara",
                "image" => "1724778403.jpg",
                "link_url" => null,
                "link_caption" => null,
                "enabled" => 1,
                "created_by" => 1,
                "published_at" => "2021-07-01"
            ],
            [
                "title" => "PeraCom wins a Gold at Global Robotics Games 2023 at Singapore",
                "description" => "<p>We are extremely proud to announce that a group of five third year undergraduates of Department of Computer Engineering, University of Peradeniya have become first in the senior university category at Global Robotics Games 2023. The three day robotics competition held in Singapore, began on the 15th of November 2023. The competition is part of the educational initiative led by&nbsp;<em>WEFAA Robotics</em>, to engage and inspire students ranging from primary to undergraduate levels.</p><p>Five undergraduate students from the Department of Computer Engineering at the University of Peradeniya, namely&nbsp;<em>Adeepa Fernando, Hariharan Raveenthiran, Piumal Rathnayake, Saadia Jameel and Thamish Wanduragala</em>, presently engaged in their industrial training in Singapore, participated in the contest under the Senior University Category. Participants had to assemble their own reconfigurable, modular and programmable robots for a soccer competition.</p><p>On day one, the students embraced the challenge of showcasing their technical prowess by assembling their version of a soccer- playing robot. On day two, the focus shifted to refining the robot’s skills and strategy in preparation for taking on opponents in a series of soccer matches. After having played all round- robin matches and winning the quarter finals on the second day, our team went on to winning the semi- final and final matches on the third day. They were thus crowned the winners of the Transformer Robot Soccer League in the Senior University Category.</p><p>After the awards ceremony, was a cultural event where participants from all countries took to the stage to showcase their respective cultures through song, dance, or drama. Our team from Sri Lanka performed the song “Sri Lanka Matha” by the renowned Sri Lankan singers Bathiya and Santhush.</p>",
                "url" => "peracom-wins-a-gold-at-global-robotics-games-2023",
                "image" => "1724778065.jpg",
                "link_url" => null,
                "link_caption" => null,
                "enabled" => 1,
                "created_by" => 1,
                "published_at" => "2023-11-29"
            ]
        ];

        foreach ($news as $item) {
            $item['description'] = str_replace('\/', '/', $item['description']);
            News::create($item);
        }

        $this->enableForeignKeys();
    }
}