<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectImagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_project_with_multiple_images()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $image1 = UploadedFile::fake()->image('image1.jpg');
        $image2 = UploadedFile::fake()->image('image2.jpg');

        $response = $this->post(route('admin.projects.store'), [
            'title' => 'My Multi Image Project',
            'description' => 'This is a description',
            'images' => [$image1, $image2],
        ]);

        $response->assertRedirect();
        
        $project = Project::first();
        $this->assertNotNull($project);
        $this->assertEquals('My Multi Image Project', $project->title);

        // Check relationship
        $this->assertCount(2, $project->images);

        // Check fallback backward compatibility column contains first image path
        $this->assertNotNull($project->image_path);
        $this->assertEquals($project->images[0]->image_path, $project->image_path);

        // Assert files exist in storage
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $project->images[0]->image_path));
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $project->images[1]->image_path));
    }

    public function test_user_can_delete_individual_image_via_ajax()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $project = Project::create([
            'user_id' => $user->id,
            'title' => 'Project A',
            'description' => 'Desc A',
        ]);

        $img1 = $project->images()->create([
            'image_path' => '/storage/projects/img1.jpg'
        ]);
        $img2 = $project->images()->create([
            'image_path' => '/storage/projects/img2.jpg'
        ]);

        // Sync fallback path
        $project->update(['image_path' => $img1->image_path]);

        // Put fake file on disk
        Storage::disk('public')->put('projects/img1.jpg', 'fake content');
        Storage::disk('public')->put('projects/img2.jpg', 'fake content');

        // Delete first image
        $response = $this->delete(route('admin.project-images.destroy', $img1->id));

        $response->assertJson([
            'success' => true,
            'fallback_path' => $img2->image_path,
        ]);

        // Assert DB counts
        $this->assertDatabaseMissing('project_images', ['id' => $img1->id]);
        $this->assertDatabaseHas('project_images', ['id' => $img2->id]);

        // Assert file deleted from storage
        Storage::disk('public')->assertMissing('projects/img1.jpg');
        Storage::disk('public')->assertExists('projects/img2.jpg');

        // Assert fallback path updated on parent project
        $project->refresh();
        $this->assertEquals($img2->image_path, $project->image_path);
    }

    public function test_deleting_project_cleans_up_all_images_from_storage()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $project = Project::create([
            'user_id' => $user->id,
            'title' => 'Project B',
            'description' => 'Desc B',
        ]);

        $img1 = $project->images()->create([
            'image_path' => '/storage/projects/img1.jpg'
        ]);
        $img2 = $project->images()->create([
            'image_path' => '/storage/projects/img2.jpg'
        ]);

        Storage::disk('public')->put('projects/img1.jpg', 'fake content');
        Storage::disk('public')->put('projects/img2.jpg', 'fake content');

        $response = $this->delete(route('admin.projects.destroy', $project->id));
        $response->assertRedirect();

        // Assert all images missing from database and storage
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        $this->assertDatabaseMissing('project_images', ['project_id' => $project->id]);
        Storage::disk('public')->assertMissing('projects/img1.jpg');
        Storage::disk('public')->assertMissing('projects/img2.jpg');
    }

    public function test_user_can_create_project_with_video_and_base64_thumbnail()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $video = UploadedFile::fake()->create('project_video.mp4', 1000, 'video/mp4');
        $thumbFile = UploadedFile::fake()->image('thumb.jpg');
        $thumbnailBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($thumbFile->getRealPath()));

        $response = $this->post(route('admin.projects.store'), [
            'title' => 'My Video Project',
            'description' => 'This is a video description',
            'video' => $video,
            'video_thumbnail' => $thumbnailBase64,
        ]);

        $response->assertRedirect();

        $project = Project::first();
        $this->assertNotNull($project);
        $this->assertNotNull($project->video_path);
        $this->assertNotNull($project->video_thumbnail_path);

        // Assert files exist in public storage
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $project->video_path));
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $project->video_thumbnail_path));
    }

    public function test_user_can_update_project_video_and_base64_thumbnail()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $project = Project::create([
            'user_id' => $user->id,
            'title' => 'Old Project Title',
            'description' => 'Old description',
            'video_path' => '/storage/projects/old_video.mp4',
            'video_thumbnail_path' => '/storage/projects/thumbnails/old_thumb.jpg',
        ]);

        // Place fake files in storage to test deletion on update
        Storage::disk('public')->put('projects/old_video.mp4', 'old video contents');
        Storage::disk('public')->put('projects/thumbnails/old_thumb.jpg', 'old thumbnail contents');

        $newVideo = UploadedFile::fake()->create('new_video.mp4', 2000, 'video/mp4');
        $newThumbFile = UploadedFile::fake()->image('new_thumb.jpg');
        $newThumbnailBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($newThumbFile->getRealPath()));

        $response = $this->put(route('admin.projects.update', $project->id), [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'video' => $newVideo,
            'video_thumbnail' => $newThumbnailBase64,
        ]);

        $response->assertRedirect();

        $project->refresh();
        $this->assertEquals('Updated Title', $project->title);
        $this->assertNotEquals('/storage/projects/old_video.mp4', $project->video_path);
        $this->assertNotEquals('/storage/projects/thumbnails/old_thumb.jpg', $project->video_thumbnail_path);

        // Assert old files deleted from storage
        Storage::disk('public')->assertMissing('projects/old_video.mp4');
        Storage::disk('public')->assertMissing('projects/thumbnails/old_thumb.jpg');

        // Assert new files exist in storage
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $project->video_path));
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $project->video_thumbnail_path));
    }

    public function test_deleting_project_cleans_up_video_and_thumbnail_files()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $project = Project::create([
            'user_id' => $user->id,
            'title' => 'Video Project for Deletion',
            'description' => 'Description',
            'video_path' => '/storage/projects/del_video.mp4',
            'video_thumbnail_path' => '/storage/projects/thumbnails/del_thumb.jpg',
        ]);

        Storage::disk('public')->put('projects/del_video.mp4', 'video content');
        Storage::disk('public')->put('projects/thumbnails/del_thumb.jpg', 'thumb content');

        $response = $this->delete(route('admin.projects.destroy', $project->id));
        $response->assertRedirect();

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        Storage::disk('public')->assertMissing('projects/del_video.mp4');
        Storage::disk('public')->assertMissing('projects/thumbnails/del_thumb.jpg');
    }

    public function test_bulk_deleting_projects_cleans_up_all_videos_and_thumbnails()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $project1 = Project::create([
            'user_id' => $user->id,
            'title' => 'P1',
            'description' => 'D1',
            'video_path' => '/storage/projects/v1.mp4',
            'video_thumbnail_path' => '/storage/projects/thumbnails/t1.jpg',
        ]);
        $project2 = Project::create([
            'user_id' => $user->id,
            'title' => 'P2',
            'description' => 'D2',
            'video_path' => '/storage/projects/v2.mp4',
            'video_thumbnail_path' => '/storage/projects/thumbnails/t2.jpg',
        ]);

        Storage::disk('public')->put('projects/v1.mp4', 'v1');
        Storage::disk('public')->put('projects/thumbnails/t1.jpg', 't1');
        Storage::disk('public')->put('projects/v2.mp4', 'v2');
        Storage::disk('public')->put('projects/thumbnails/t2.jpg', 't2');

        $response = $this->post(route('admin.projects.bulk-delete'), [
            'ids' => [$project1->id, $project2->id],
        ]);
        $response->assertRedirect();

        $this->assertDatabaseMissing('projects', ['id' => $project1->id]);
        $this->assertDatabaseMissing('projects', ['id' => $project2->id]);
        Storage::disk('public')->assertMissing('projects/v1.mp4');
        Storage::disk('public')->assertMissing('projects/thumbnails/t1.jpg');
        Storage::disk('public')->assertMissing('projects/v2.mp4');
        Storage::disk('public')->assertMissing('projects/thumbnails/t2.jpg');
    }
}

