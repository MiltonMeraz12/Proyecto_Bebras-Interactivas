<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FlujoBebrasTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function testNoUsuario()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                    ->visit('/conjuntos')
                    ->pause(500)
                    ->assertPathIs('/login'); 
        });
    }

    public function testFlujoAdmin()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->waitFor('input[name="email"]')
                    ->type('email', 'admin@bebras.mx')
                    ->type('password', 'temporal1')
                    ->click('button[type="submit"]') 
                    ->pause(1000);

            $browser->assertPathIs('/admin/dashboard')
                    ->assertSee('Panel de Administración')
                    ->assertSee('Alumno Test'); 

            $browser->visit('/admin/conjuntos/1')
                    ->pause(1000)
                    ->assertSee('Libros Populares')
                    ->assertSee('Tutorial de Dibujo')
                    ->assertSee('Receta de Hamburguesas')
                    ->assertSee('5 Dulces')
                    ->assertSee('¿Dónde quedó la bolita?')
                    ->assertSee('Palillos Chinos');
        });
    }

    public function testFlujoAlumno()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'alumno@bebras.mx')
                    ->type('password', 'temporal1')
                    ->click('button[type="submit"]') 
                    ->pause(1000);

            $browser->assertPathIs('/conjuntos')
                    ->assertSee('Reto Bebras MX — Primavera 2025');

            $browser->clickLink('Ver conjunto') 
                    ->pause(1000)
                    ->assertSee('Iniciar conjunto')
                    ->press('Iniciar conjunto')
                    ->pause(1500); 

            $browser->assertSee('Libros Populares')
                    ->assertSee('¿Cuál libro es el que los niños piden más seguido');

            $browser->click('img[src*="oruga.png"]')
                    ->pause(500);

            $browser->press('Verificar respuesta') 
                    ->pause(1000)
                    ->assertSee('¡Correcto!'); 

            $browser->visit('/conjuntos/1/preguntas/57')
                    ->pause(1000)
                    ->press('Finalizar conjunto')
                    ->pause(1000)
                    ->assertSee('Detalle de respuestas');

            $browser->visit('/conjuntos')
                    ->pause(1000)
                    ->assertSee('Reto Bebras MX — Primavera 2025')
                    ->assertSee('Ver resultados'); 
        });
    }

    public function testSeguridadAlumnoAdmin()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('email', 'alumno@bebras.mx')->first())
                    ->visit('/admin/dashboard')
                    ->pause(500)
                    ->assertPathIsNot('/admin/dashboard'); 
        });
    }
}