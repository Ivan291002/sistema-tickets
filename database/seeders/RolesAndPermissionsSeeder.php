<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Comentario;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // -------------------------
        // PERMISOS
        // -------------------------

        $crear = Permission::firstOrCreate(['name' => 'crear tickets']);
        $ver = Permission::firstOrCreate(['name' => 'ver tickets']);
        $editar = Permission::firstOrCreate(['name' => 'editar tickets']);
        $cerrar = Permission::firstOrCreate(['name' => 'cerrar tickets']);
        $gestionarUsuarios = Permission::firstOrCreate(['name' => 'gestionar usuarios']);

        // -------------------------
        // ROLES
        // -------------------------

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $supervisor = Role::firstOrCreate(['name' => 'supervisor']);
        $usuario = Role::firstOrCreate(['name' => 'usuario']);

        // -------------------------
        // PERMISOS DEL ADMIN
        // -------------------------

        $admin->syncPermissions([$crear, $ver, $editar, $cerrar, $gestionarUsuarios]);

        // -------------------------
        // PERMISOS DEL SUPERVISOR
        // -------------------------

        $supervisor->syncPermissions([$crear, $ver, $editar, $cerrar]);

        // -------------------------
        // PERMISOS DEL USUARIO
        // -------------------------

        $usuario->syncPermissions([$crear, $ver]);

        // -------------------------
        // USUARIOS PREDEFINIDOS
        // -------------------------

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            ['name' => 'Administrador General', 'password' => Hash::make('12345678')]
        );
        $adminUser->syncRoles(['admin']);

        $supervisorUser = User::firstOrCreate(
            ['email' => 'supervisor@test.com'],
            ['name' => 'Carlos Supervisor', 'password' => Hash::make('12345678')]
        );
        $supervisorUser->syncRoles(['supervisor']);

        $normalUser = User::firstOrCreate(
            ['email' => 'usuario@test.com'],
            ['name' => 'María Usuaria', 'password' => Hash::make('12345678')]
        );
        $normalUser->syncRoles(['usuario']);

        // -------------------------
        // CATEGORÍAS PREDEFINIDAS
        // -------------------------

        $catHardware = Categoria::firstOrCreate(
            ['nombre' => 'Hardware'],
            ['color' => 'emerald', 'descripcion' => 'Problemas con equipos físicos, monitores, periféricos e impresoras.']
        );

        $catSoftware = Categoria::firstOrCreate(
            ['nombre' => 'Software'],
            ['color' => 'purple', 'descripcion' => 'Incidencias con aplicaciones, licencias y sistemas operativos.']
        );

        $catRedes = Categoria::firstOrCreate(
            ['nombre' => 'Redes y Conectividad'],
            ['color' => 'amber', 'descripcion' => 'Fallas de conexión a Internet, VPN, Wi-Fi o cableado estructurado.']
        );

        $catSoporte = Categoria::firstOrCreate(
            ['nombre' => 'Soporte General'],
            ['color' => 'blue', 'descripcion' => 'Consultas generales, accesos y configuración de cuentas.']
        );

        $catFacturacion = Categoria::firstOrCreate(
            ['nombre' => 'Facturación'],
            ['color' => 'rose', 'descripcion' => 'Consultas administrativas, pagos y emisión de comprobantes.']
        );

        // -------------------------
        // TICKETS DE MUESTRA (DEMO TP)
        // -------------------------

        if (Ticket::count() === 0) {
            $ticket1 = Ticket::create([
                'user_id' => $normalUser->id,
                'categoria_id' => $catRedes->id,
                'asignado_a' => $supervisorUser->id,
                'titulo' => 'Sin conexión a Internet en la oficina técnica',
                'descripcion' => 'Desde esta mañana no podemos acceder a los servidores locales ni a Internet en el sector B.',
                'prioridad' => 'urgente',
                'estado' => 'en_proceso',
            ]);

            Comentario::create([
                'ticket_id' => $ticket1->id,
                'user_id' => $normalUser->id,
                'contenido' => 'Reiniciamos el switch secundario pero las luces siguen parpadeando en rojo.',
            ]);

            Comentario::create([
                'ticket_id' => $ticket1->id,
                'user_id' => $supervisorUser->id,
                'contenido' => 'Hola María, estamos revisando el enlace principal del proveedor. Un técnico pasará por el sector en 20 minutos.',
            ]);

            $ticket2 = Ticket::create([
                'user_id' => $normalUser->id,
                'categoria_id' => $catHardware->id,
                'asignado_a' => null,
                'titulo' => 'Impresora láser no toma papel en recepción',
                'descripcion' => 'La impresora multifunción marca atasco de papel permanente aunque la bandeja está limpia.',
                'prioridad' => 'media',
                'estado' => 'abierto',
            ]);

            $ticket3 = Ticket::create([
                'user_id' => $normalUser->id,
                'categoria_id' => $catSoftware->id,
                'asignado_a' => $adminUser->id,
                'titulo' => 'Solicitud de licencia para software de diseño',
                'descripcion' => 'Se requiere habilitar la cuenta para el nuevo diseñador incorporado este mes.',
                'prioridad' => 'alta',
                'estado' => 'cerrado',
            ]);

            Comentario::create([
                'ticket_id' => $ticket3->id,
                'user_id' => $adminUser->id,
                'contenido' => 'Licencia asignada correctamente. Credenciales enviadas por correo seguro.',
            ]);
        }
    }
}
