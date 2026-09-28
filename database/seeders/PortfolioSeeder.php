<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Achievement;
use App\Models\Skill;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PortfolioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the admin user
        $user = User::where('email', 'admin@portfolio.com')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Admin',
                'email' => 'admin@portfolio.com',
                'password' => bcrypt('password'),
            ]);
        }
        $userId = $user->id;

        // Truncate existing data to prevent duplicates
        Schema::disableForeignKeyConstraints();
        DB::table('profiles')->truncate();
        DB::table('education')->truncate();
        DB::table('experiences')->truncate();
        DB::table('achievements')->truncate();
        DB::table('skills')->truncate();
        DB::table('projects')->truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Seed Profile
        Profile::create([
            'user_id' => $userId,
            'name' => 'Abidsyach Pramana',
            'headline' => 'A Bachelor Degree of Computer Science at Binus University',
            'photo_path' => null,
            'phone' => '+6281228822615',
            'email' => 'Abidsyach1501@gmail.com',
            'linkedin_url' => 'http://www.linkedin.com/in/abidsyach-pramana',
            'instagram_url' => 'https://instagram.com/abidsyachp?igshid=1c19s9bhxl8nd',
            'cv_path' => null,
            'about_text' => "A bachelor's degree in computer science at Binus University with a passion for technology looking for a meaningful role to start a career in Information Technology and always wants to learn new things. Skilled in creating websites, Android applications, data analytics and designing user interface applications with a great desire to always develop self-abilities that can be useful to help others.",
        ]);

        // 2. Seed Education
        Education::create([
            'user_id' => $userId,
            'institution' => 'Bina Nusantara University',
            'degree' => 'Bachelor of Computer Science',
            'major' => 'Computer Science',
            'start_date' => 'Aug 2018',
            'end_date' => 'Jul 2022',
            'gpa' => '3.06',
            'description' => "Learn about how to become an IT. Working on several given projects such as creating a website using Microsoft Visual Studio Code in PHP, C and using MySQL such as creating a website containing information on beaches and mountains, then a website for online shopping, then creating several Android applications using the Java programming language such as applications for heart health, applications for listening to music and watching videos and applications for online shopping.",
            'sort_order' => 1,
        ]);

        // 3. Seed Experiences
        Experience::create([
            'user_id' => $userId,
            'company' => 'Google Bangkit',
            'role' => 'Cloud Computing Cohort',
            'start_date' => 'Feb 2021',
            'end_date' => 'Jun 2021',
            'description' => "Completed a 6 month learning program to gain knowledge about Cloud Computing by completing multiple course and get certificate: Web Development Basic, Google IT Support, Google IT Automation with Python, Architecting with Google Compute Engine, From Data to Insights with Google Cloud Platform. Develop soft skills with assignments and communicate with team members from various paths.",
            'sort_order' => 1,
        ]);

        Experience::create([
            'user_id' => $userId,
            'company' => 'IT Maturity Assessment Project',
            'role' => 'Project Consultant Assistant',
            'start_date' => 'Jun 2021',
            'end_date' => 'Jan 2022',
            'description' => "Participate and help work on projects regarding IT Maturity Assessment for several companies such as Injourney and Inalum using Cobit 2019 & Cobit 5. Participate in helping work on projects regarding electronic-based government systems for client companies.",
            'sort_order' => 2,
        ]);

        // 4. Seed Achievements
        Achievement::create([
            'user_id' => $userId,
            'title' => 'Web Development Basic Certificate',
            'issuer' => 'Dicoding',
            'date' => '2021',
            'credential_url' => 'https://4ddf077f-7c3c-4d38-9b60-1d2d7b428ea3.filesusr.com/ugd/4a6bec_3b87f52a919b4a54be9dcbf1782d1116.pdf',
            'sort_order' => 1,
        ]);

        Achievement::create([
            'user_id' => $userId,
            'title' => 'Qwiklabs Badge (BigQuery, GCP Machine Learning, etc.)',
            'issuer' => 'Google Qwiklabs',
            'date' => '2021',
            'credential_url' => 'https://www.qwiklabs.com/public_profiles/bb9c0b07-acb1-4b79-9f7a-1f4ad51ca541',
            'sort_order' => 2,
        ]);

        // 5. Seed Skills
        $skills = [
            // Technical
            ['name' => 'Figma', 'category' => 'Technical'],
            ['name' => 'Web Developer (PHP, Laravel, CI)', 'category' => 'Technical'],
            ['name' => 'Mobile Application', 'category' => 'Technical'],
            ['name' => 'HTML & CSS', 'category' => 'Technical'],
            ['name' => 'Java, C', 'category' => 'Technical'],
            ['name' => 'SQL', 'category' => 'Technical'],
            ['name' => 'Social Media Management', 'category' => 'Technical'],
            // Soft Skill
            ['name' => 'Teamwork', 'category' => 'Soft Skill'],
            ['name' => 'Creative', 'category' => 'Soft Skill'],
            ['name' => 'Easy to learn', 'category' => 'Soft Skill'],
            ['name' => 'Innovative', 'category' => 'Soft Skill'],
            // Language
            ['name' => 'Indonesia (Native)', 'category' => 'Language'],
            ['name' => 'English (Professional)', 'category' => 'Language'],
        ];

        foreach ($skills as $index => $skill) {
            Skill::create([
                'user_id' => $userId,
                'name' => $skill['name'],
                'category' => $skill['category'],
                'sort_order' => $index + 1,
            ]);
        }

        // 6. Seed Projects
        Project::create([
            'user_id' => $userId,
            'title' => 'Heartcare',
            'description' => 'I have a project that I work on in groups called Heartcare. Heartcare is a heart health care application. Then this application has special features to maintain heart health such as heart rate trackers, meal schedules, exercise schedules, medication, and emergency calls to paramedics. All features are made based on user conditions.',
            'image_path' => null,
            'video_path' => null,
            'project_url' => null,
            'sort_order' => 1,
        ]);

        Project::create([
            'user_id' => $userId,
            'title' => 'Miku Website',
            'description' => 'Then I have a website project that I work on in groups, namely Miku. Miku is a website created to describe a music concert. On this website there are menus: Home which is the initial display of the website; About which contains an explanation of the music concert such as where the concert is held and the guest stars who will sing at the concert; Concert Gallery contains photos of guest stars who will perform at the concert; Merchandise Store to buy merchandise from concerts; Buy tickets to buy concert tickets.',
            'image_path' => null,
            'video_path' => null,
            'project_url' => null,
            'sort_order' => 2,
        ]);

        Project::create([
            'user_id' => $userId,
            'title' => 'Covid-19 Application',
            'description' => 'I created an individual project, which is an application that contains the sign in, sign up feature, news about Covid-19, data about the Covid-19 virus (number of people infected, recovered and died), locations about parts that have spread the Covid-19 virus, and features for the location of the hospital and emergency calls that can call the medical staff of the hospital.',
            'image_path' => null,
            'video_path' => null,
            'project_url' => null,
            'sort_order' => 3,
        ]);

        Project::create([
            'user_id' => $userId,
            'title' => 'Wonderful Journey',
            'description' => 'Wonderful Journey is a tourism blog website that is used to see various beaches and mountains in Indonesia. This website uses a database that is used to store data - beach data, mountains and user data.',
            'image_path' => null,
            'video_path' => null,
            'project_url' => null,
            'sort_order' => 4,
        ]);

        Project::create([
            'user_id' => $userId,
            'title' => 'Pet Care',
            'description' => 'Pet Care is an application design for animal lovers that contains the types of animals, food for animals, accessories and the nearest veterinary clinic.',
            'image_path' => null,
            'video_path' => null,
            'project_url' => null,
            'sort_order' => 5,
        ]);
    }
}
