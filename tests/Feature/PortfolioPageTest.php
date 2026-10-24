<?php

namespace Tests\Feature;

use Tests\TestCase;

class PortfolioPageTest extends TestCase
{
    public function test_public_portfolio_pages_render(): void
    {
        foreach (['experience', 'projects', 'education', 'skills'] as $page) {
            $this->get('/'.$page)->assertOk()->assertSee('Sanish Maharjan');
        }
    }

    public function test_education_and_skills_metadata_identifies_the_portfolio(): void
    {
        foreach (['education', 'skills'] as $page) {
            $this->get('/'.$page)
                ->assertSee('rel="canonical"', false)
                ->assertDontSee('portfolio.smaranjitghose.codes')
                ->assertDontSee('free portfolio template');
        }
    }

    public function test_resume_download_asset_exists(): void
    {
        $path = public_path('Sanish_Maharjan_Resume_2026.pdf');

        $this->assertFileExists($path);
        $this->assertSame('%PDF-', file_get_contents($path, false, null, 0, 5));
    }

    public function test_bundled_portrait_is_a_valid_image(): void
    {
        $path = public_path('images/Portrait/pp.jpg');

        $this->assertFileExists($path);
        $dimensions = getimagesize($path);
        $this->assertIsArray($dimensions);
        $this->assertSame('image/jpeg', $dimensions['mime']);
        $this->assertGreaterThan(0, $dimensions[0]);
        $this->assertGreaterThan(0, $dimensions[1]);
    }
}
