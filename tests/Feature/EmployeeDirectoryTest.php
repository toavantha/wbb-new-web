<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmployeeDirectoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_directory_only_displays_published_employees(): void
    {
        Employee::factory()->create([
            'name' => 'Published Employee',
            'designation' => 'Regional Officer',
            'mobile' => '0712345678',
            'email' => 'published@example.com',
            'is_active' => true,
        ]);
        Employee::factory()->create([
            'name' => 'Hidden Employee',
            'is_active' => false,
        ]);

        $response = $this->get(route('employees'));

        $response->assertOk();
        $response->assertSee('Published Employee');
        $response->assertSee('Regional Officer');
        $response->assertSee('0712345678');
        $response->assertSee('published@example.com');
        $response->assertDontSee('Hidden Employee');
    }

    public function test_public_directory_displays_employee_names_and_designations_in_the_selected_locale(): void
    {
        Employee::factory()->create([
            'name' => 'English Employee Name',
            'name_si' => 'සිංහල සේවක නම',
            'name_ta' => 'தமிழ் பணியாளர் பெயர்',
            'designation' => 'English Designation',
            'designation_si' => 'සිංහල තනතුර',
            'designation_ta' => 'தமிழ் பதவி',
        ]);

        foreach ([
            'en' => ['English Employee Name', 'English Designation'],
            'si' => ['සිංහල සේවක නම', 'සිංහල තනතුර'],
            'ta' => ['தமிழ் பணியாளர் பெயர்', 'தமிழ் பதவி'],
        ] as $locale => [$name, $designation]) {
            $response = $this->withSession(['locale' => $locale])->get(route('employees'));

            $response->assertOk()
                ->assertSee($name)
                ->assertSee($designation);

            if ($locale !== 'en') {
                $response->assertDontSee('English Employee Name')
                    ->assertDontSee('English Designation');
            }
        }
    }

    public function test_public_directory_falls_back_to_english_when_an_employee_translation_is_missing(): void
    {
        Employee::factory()->create([
            'name' => 'English Employee Name',
            'designation' => 'English Designation',
            'name_si' => '',
            'designation_si' => '',
        ]);

        $this->withSession(['locale' => 'si'])
            ->get(route('employees'))
            ->assertOk()
            ->assertSee('English Employee Name')
            ->assertSee('English Designation');
    }

    public function test_employee_management_requires_authentication(): void
    {
        $response = $this->get(route('admin.employees.index'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_authenticated_admin_can_view_employee_management_pages(): void
    {
        $employee = Employee::factory()->create(['name' => 'Existing Employee']);
        $this->actingAs(User::factory()->create());

        $this->get(route('admin.employees.index'))
            ->assertOk()
            ->assertSee('Existing Employee');
        $this->get(route('admin.employees.create'))
            ->assertOk()
            ->assertSee('Employee Photo')
            ->assertSee('inputmode="numeric"', false)
            ->assertSee('pattern="0[0-9]{9}"', false)
            ->assertSee('English Content')
            ->assertSee('Sinhala Content')
            ->assertSee('Tamil Content');
        $this->get(route('admin.employees.edit', $employee))
            ->assertOk()
            ->assertSee('Existing Employee')
            ->assertSee($employee->designation)
            ->assertSee('inputmode="numeric"', false)
            ->assertSee('pattern="0[0-9]{9}"', false);
    }

    public function test_authenticated_admin_can_create_employee_with_photo(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());

        $response = $this->post(route('admin.employees.store'), [
            'name' => 'Nadeesha Perera',
            'name_si' => 'නදීෂා පෙරේරා',
            'name_ta' => 'நதீஷா பெரேரா',
            'designation' => 'Programme Officer',
            'designation_si' => 'වැඩසටහන් නිලධාරිනී',
            'designation_ta' => 'நிகழ்ச்சித் திட்ட அலுவலர்',
            'mobile' => '0712345678',
            'email' => 'nadeesha@example.com',
            'image' => UploadedFile::fake()->image('employee.png'),
            'is_active' => '1',
        ]);

        $employee = Employee::query()->where('email', 'nadeesha@example.com')->firstOrFail();

        $response->assertRedirect(route('admin.employees.index'));
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'name' => 'Nadeesha Perera',
            'name_si' => 'නදීෂා පෙරේරා',
            'name_ta' => 'நதீஷா பெரேரா',
            'designation' => 'Programme Officer',
            'designation_si' => 'වැඩසටහන් නිලධාරිනී',
            'designation_ta' => 'நிகழ்ச்சித் திட்ட அலுவலர்',
            'mobile' => '0712345678',
            'email' => 'nadeesha@example.com',
            'is_active' => true,
        ]);
        Storage::disk('public')->assertExists($employee->image_path);
    }

    public function test_admin_must_provide_employee_names_and_designations_in_all_languages(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->from(route('admin.employees.create'))
            ->post(route('admin.employees.store'), [
                'name' => 'English Employee',
                'designation' => 'English Designation',
                'name_si' => '',
                'name_ta' => '',
                'designation_si' => '',
                'designation_ta' => '',
            ]);

        $response->assertRedirect(route('admin.employees.create'))
            ->assertSessionHasErrors(['name_si', 'name_ta', 'designation_si', 'designation_ta']);
        $this->assertDatabaseMissing('employees', ['name' => 'English Employee']);
    }

    public function test_employee_mobile_must_be_ten_digits_starting_with_zero_when_creating_or_updating(): void
    {
        $this->actingAs(User::factory()->create());

        $employeeInput = [
            'name' => 'Employee Name',
            'name_si' => 'සේවක නම',
            'name_ta' => 'பணியாளர் பெயர்',
            'designation' => 'Officer',
            'designation_si' => 'නිලධාරී',
            'designation_ta' => 'அலுவலர்',
        ];

        foreach (['071234567', '07123456789', '1712345678', '07123abc89'] as $mobile) {
            $response = $this->from(route('admin.employees.create'))
                ->post(route('admin.employees.store'), array_merge($employeeInput, ['mobile' => $mobile]));

            $response->assertRedirect(route('admin.employees.create'))
                ->assertSessionHasErrors([
                    'mobile' => __('site.contact.phone_format_error'),
                ]);
        }

        $this->assertDatabaseCount('employees', 0);

        $employee = Employee::factory()->create(['mobile' => '0711111111']);
        $response = $this->from(route('admin.employees.edit', $employee))
            ->put(route('admin.employees.update', $employee), array_merge($employeeInput, ['mobile' => '077123456a']));

        $response->assertRedirect(route('admin.employees.edit', $employee))
            ->assertSessionHasErrors([
                'mobile' => __('site.contact.phone_format_error'),
            ]);
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'mobile' => '0711111111',
        ]);
    }

    public function test_employee_photo_must_be_an_image(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());

        $response = $this->from(route('admin.employees.create'))->post(route('admin.employees.store'), [
            'name' => 'Nadeesha Perera',
            'name_si' => 'නදීෂා පෙරේරා',
            'name_ta' => 'நதீஷா பெரேரா',
            'designation' => 'Programme Officer',
            'designation_si' => 'වැඩසටහන් නිලධාරිනී',
            'designation_ta' => 'நிகழ்ச்சித் திட்ட அலுவலர்',
            'image' => UploadedFile::fake()->create('not-an-image.txt', 20, 'text/plain'),
        ]);

        $response->assertRedirect(route('admin.employees.create'));
        $response->assertSessionHasErrors('image');
        $this->assertDatabaseCount('employees', 0);
        Storage::disk('public')->assertDirectoryEmpty('employees');
    }

    public function test_admin_can_replace_employee_photo_and_hide_profile(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());
        $oldImagePath = 'employees/old-photo.png';
        Storage::disk('public')->put($oldImagePath, 'old photo');
        $employee = Employee::factory()->create([
            'image_path' => $oldImagePath,
            'is_active' => true,
        ]);

        $response = $this->put(route('admin.employees.update', $employee), [
            'name' => 'Updated Employee',
            'name_si' => 'යාවත්කාලීන සේවකයා',
            'name_ta' => 'புதுப்பிக்கப்பட்ட பணியாளர்',
            'designation' => 'Senior Officer',
            'designation_si' => 'ජ්‍යෙෂ්ඨ නිලධාරී',
            'designation_ta' => 'மூத்த அலுவலர்',
            'mobile' => '0771234567',
            'email' => 'updated@example.com',
            'image' => UploadedFile::fake()->image('updated.jpg'),
        ]);

        $employee->refresh();

        $response->assertRedirect(route('admin.employees.index'));
        $this->assertSame('Updated Employee', $employee->name);
        $this->assertSame('යාවත්කාලීන සේවකයා', $employee->name_si);
        $this->assertSame('புதுப்பிக்கப்பட்ட பணியாளர்', $employee->name_ta);
        $this->assertSame('ජ්‍යෙෂ්ඨ නිලධාරී', $employee->designation_si);
        $this->assertSame('மூத்த அலுவலர்', $employee->designation_ta);
        $this->assertFalse($employee->is_active);
        $this->assertNotSame($oldImagePath, $employee->image_path);
        Storage::disk('public')->assertMissing($oldImagePath);
        Storage::disk('public')->assertExists($employee->image_path);
    }

    public function test_admin_can_add_photo_to_employee_without_existing_photo(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());
        $employee = Employee::factory()->create([
            'image_path' => null,
            'mobile' => '0712345678',
        ]);

        $response = $this->put(route('admin.employees.update', $employee), [
            'name' => $employee->name,
            'name_si' => $employee->name,
            'name_ta' => $employee->name,
            'designation' => $employee->designation,
            'designation_si' => $employee->designation,
            'designation_ta' => $employee->designation,
            'mobile' => $employee->mobile,
            'email' => $employee->email,
            'image' => UploadedFile::fake()->image('new-photo.png'),
            'is_active' => '1',
        ]);

        $employee->refresh();

        $response->assertRedirect(route('admin.employees.index'));
        $this->assertNotNull($employee->image_path);
        Storage::disk('public')->assertExists($employee->image_path);
    }

    public function test_admin_can_delete_employee_and_photo(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());
        $imagePath = 'employees/employee.png';
        Storage::disk('public')->put($imagePath, 'photo');
        $employee = Employee::factory()->create(['image_path' => $imagePath]);

        $response = $this->delete(route('admin.employees.destroy', $employee));

        $response->assertRedirect(route('admin.employees.index'));
        $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
        Storage::disk('public')->assertMissing($imagePath);
    }
}
