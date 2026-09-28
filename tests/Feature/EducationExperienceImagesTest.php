<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Education;
use App\Models\EducationImage;
use App\Models\Experience;
use App\Models\ExperienceImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EducationExperienceImagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_education_with_multiple_images()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $image1 = UploadedFile::fake()->image('cert1.jpg');
        $image2 = UploadedFile::fake()->image('cert2.jpg');

        $response = $this->post(route('admin.education.store'), [
            'institution' => 'Test University',
            'degree' => 'Bachelor of Testing',
            'start_date' => '2020',
            'end_date' => '2024',
            'images' => [$image1, $image2],
        ]);

        $response->assertRedirect();
        
        $education = Education::first();
        $this->assertNotNull($education);
        $this->assertCount(2, $education->images);

        Storage::disk('public')->assertExists(str_replace('/storage/', '', $education->images[0]->image_path));
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $education->images[1]->image_path));
    }

    public function test_user_can_update_education_with_images()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $education = Education::create([
            'user_id' => $user->id,
            'institution' => 'Old Univ',
            'degree' => 'Old Degree',
            'start_date' => '2019',
            'end_date' => '2023',
        ]);

        $image = UploadedFile::fake()->image('new_cert.jpg');

        $response = $this->put(route('admin.education.update', $education->id), [
            'institution' => 'New Univ',
            'degree' => 'New Degree',
            'start_date' => '2019',
            'end_date' => '2023',
            'images' => [$image],
        ]);

        $response->assertRedirect();
        $education->refresh();
        $this->assertEquals('New Univ', $education->institution);
        $this->assertCount(1, $education->images);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $education->images[0]->image_path));
    }

    public function test_user_can_delete_education_image_via_ajax()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $education = Education::create([
            'user_id' => $user->id,
            'institution' => 'Univ A',
            'degree' => 'Degree A',
            'start_date' => '2019',
            'end_date' => '2023',
        ]);

        $img = $education->images()->create([
            'image_path' => '/storage/education/cert1.jpg'
        ]);

        Storage::disk('public')->put('education/cert1.jpg', 'fake content');

        $response = $this->delete(route('admin.education-images.destroy', $img->id));
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('education_images', ['id' => $img->id]);
        Storage::disk('public')->assertMissing('education/cert1.jpg');
    }

    public function test_deleting_education_cleans_up_images_from_storage()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $education = Education::create([
            'user_id' => $user->id,
            'institution' => 'Univ B',
            'degree' => 'Degree B',
            'start_date' => '2019',
            'end_date' => '2023',
        ]);

        $img = $education->images()->create([
            'image_path' => '/storage/education/cert2.jpg'
        ]);

        Storage::disk('public')->put('education/cert2.jpg', 'fake content');

        $response = $this->delete(route('admin.education.destroy', $education->id));
        $response->assertRedirect();

        $this->assertDatabaseMissing('education', ['id' => $education->id]);
        Storage::disk('public')->assertMissing('education/cert2.jpg');
    }

    public function test_user_can_create_experience_with_multiple_images()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $image1 = UploadedFile::fake()->image('exp1.jpg');
        $image2 = UploadedFile::fake()->image('exp2.jpg');

        $response = $this->post(route('admin.experience.store'), [
            'company' => 'Test Company',
            'role' => 'Tester',
            'start_date' => '2020',
            'end_date' => '2021',
            'description' => 'Tested code',
            'images' => [$image1, $image2],
        ]);

        $response->assertRedirect();
        
        $experience = Experience::first();
        $this->assertNotNull($experience);
        $this->assertCount(2, $experience->images);

        Storage::disk('public')->assertExists(str_replace('/storage/', '', $experience->images[0]->image_path));
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $experience->images[1]->image_path));
    }

    public function test_user_can_delete_experience_image_via_ajax()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $experience = Experience::create([
            'user_id' => $user->id,
            'company' => 'Comp A',
            'role' => 'Role A',
            'start_date' => '2019',
            'end_date' => '2020',
            'description' => 'Desc A',
        ]);

        $img = $experience->images()->create([
            'image_path' => '/storage/experience/exp1.jpg'
        ]);

        Storage::disk('public')->put('experience/exp1.jpg', 'fake content');

        $response = $this->delete(route('admin.experience-images.destroy', $img->id));
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('experience_images', ['id' => $img->id]);
        Storage::disk('public')->assertMissing('experience/exp1.jpg');
    }

    public function test_deleting_experience_cleans_up_images_from_storage()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $experience = Experience::create([
            'user_id' => $user->id,
            'company' => 'Comp B',
            'role' => 'Role B',
            'start_date' => '2019',
            'end_date' => '2020',
            'description' => 'Desc B',
        ]);

        $img = $experience->images()->create([
            'image_path' => '/storage/experience/exp2.jpg'
        ]);

        Storage::disk('public')->put('experience/exp2.jpg', 'fake content');

        $response = $this->delete(route('admin.experience.destroy', $experience->id));
        $response->assertRedirect();

        $this->assertDatabaseMissing('experiences', ['id' => $experience->id]);
        Storage::disk('public')->assertMissing('experience/exp2.jpg');
    }

    public function test_bulk_deleting_education_cleans_up_images()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $edu1 = Education::create([
            'user_id' => $user->id,
            'institution' => 'U1',
            'degree' => 'D1',
            'start_date' => '2010',
            'end_date' => '2012',
        ]);
        $edu2 = Education::create([
            'user_id' => $user->id,
            'institution' => 'U2',
            'degree' => 'D2',
            'start_date' => '2012',
            'end_date' => '2014',
        ]);

        $img1 = $edu1->images()->create(['image_path' => '/storage/education/img1.jpg']);
        $img2 = $edu2->images()->create(['image_path' => '/storage/education/img2.jpg']);

        Storage::disk('public')->put('education/img1.jpg', 'c1');
        Storage::disk('public')->put('education/img2.jpg', 'c2');

        $response = $this->post(route('admin.education.bulk-delete'), [
            'ids' => [$edu1->id, $edu2->id],
        ]);
        $response->assertRedirect();

        $this->assertDatabaseMissing('education', ['id' => $edu1->id]);
        $this->assertDatabaseMissing('education', ['id' => $edu2->id]);
        Storage::disk('public')->assertMissing('education/img1.jpg');
        Storage::disk('public')->assertMissing('education/img2.jpg');
    }

    public function test_bulk_deleting_experience_cleans_up_images()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $exp1 = Experience::create([
            'user_id' => $user->id,
            'company' => 'C1',
            'role' => 'R1',
            'start_date' => '2010',
            'end_date' => '2012',
            'description' => 'Desc 1',
        ]);
        $exp2 = Experience::create([
            'user_id' => $user->id,
            'company' => 'C2',
            'role' => 'R2',
            'start_date' => '2012',
            'end_date' => '2014',
            'description' => 'Desc 2',
        ]);

        $img1 = $exp1->images()->create(['image_path' => '/storage/experience/img1.jpg']);
        $img2 = $exp2->images()->create(['image_path' => '/storage/experience/img2.jpg']);

        Storage::disk('public')->put('experience/img1.jpg', 'e1');
        Storage::disk('public')->put('experience/img2.jpg', 'e2');

        $response = $this->post(route('admin.experience.bulk-delete'), [
            'ids' => [$exp1->id, $exp2->id],
        ]);
        $response->assertRedirect();

        $this->assertDatabaseMissing('experiences', ['id' => $exp1->id]);
        $this->assertDatabaseMissing('experiences', ['id' => $exp2->id]);
        Storage::disk('public')->assertMissing('experience/img1.jpg');
        Storage::disk('public')->assertMissing('experience/img2.jpg');
    }
}
