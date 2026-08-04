# BarberPro — Sistema de Gestión de Barberías & Reserva Online

[![Docker](https://img.shields.io/badge/Docker-Supported-blue?logo=docker)](https://www.docker.com/)
[![Vercel](https://img.shields.io/badge/Vercel-Deployment-black?logo=vercel)](https://vercel.com/)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?logo=laravel)](https://laravel.com/)

Sistema web integral para la gestión de barberías, multi-sucursal, control de comisiones de barberos, cobros en caja, cola de espera walk-in, confirmaciones por WhatsApp y reserva de citas online en tiempo real accesibles sin iniciar sesión.

---

## 🚀 Despliegue con Docker

Para ejecutar la aplicación en contenedores aislados (Laravel + MySQL 8 + phpMyAdmin) sin depender de instalaciones locales:

```bash
# 1. Clonar el repositorio
git clone https://github.com/carlos-martinezc0620/Barber-Shop-Project-6B.git
cd Barber-Shop-Project-6B

# 2. Copiar archivo de entorno
cp .env.example .env

# 3. Levantar los contenedores
docker-compose up -d --build

# 4. Ejecutar migraciones y datos iniciales dentro del contenedor
docker-compose exec app php artisan migrate --seed
```

- **App Web:** [http://localhost:8000](http://localhost:8000)
- **Reserva Pública (sin login):** [http://localhost:8000/reservar](http://localhost:8000/reservar)
- **phpMyAdmin:** [http://localhost:8080](http://localhost:8080)

---

## ⚡ Despliegue en Vercel (Serverless)

Este repositorio incluye la configuración de producción para desplegar en **Vercel** usando `vercel.json` y Serverless Functions:

1. Importa este repositorio en tu cuenta de [Vercel](https://vercel.com).
2. Agrega las siguientes **Variables de Entorno** en el dashboard de Vercel:
   - `APP_KEY`: *(Generada con `php artisan key:generate --show`)*
   - `APP_ENV`: `production`
   - `APP_DEBUG`: `false`
   - `DB_CONNECTION`: `mysql`
   - `DB_HOST`: *(Tu host de MySQL remoto / PlanetScale / Supabase / Railway)*
   - `DB_DATABASE`: `tu_base_de_datos`
   - `DB_USERNAME`: `tu_usuario`
   - `DB_PASSWORD`: `tu_contraseña`
3. Despliega presionando **Deploy**. Vercel compilará los assets y mapeará todas las peticiones a `api/index.php`.

---

## 🔑 Credenciales y Roles de Usuario

| Rol | Correo Electrónico | Contraseña | Permisos |
| :--- | :--- | :--- | :--- |
| **👑 Admin General** | `admin@barbershop.com` | `Admin123` | Control total, reportes por sucursal, gestión global. |
| **🏢 Encargado Sucursal** | `encargado@barbershop.com` | `Encargado123` | Gestión de citas, caja y barberos de su local. |
| **✂️ Barbero** | `charly0620@barbershop.com` | `Charly123` | Agenda propia, check-in, cobro y comisiones. |
| **✂️ Barbero (Secundario)** | `manny007@barbershop.com` | `Manuel123` | Agenda propia, check-in, cobro y comisiones. |
| **👤 Cliente** | `roberto@example.com` | `barbershop2026` | Historial de citas y reserva online. |

---

## 🧪 Pruebas Unitarias e Integración

Se han implementado pruebas automáticas para validar los roles de usuario, la reserva pública sin login y el flujo de check-in:

```bash
# Ejecutar suite de pruebas
php run_tests.php
```

Resultados esperados:
```text
✅ [PASS] Evaluación de Rol: Admin General (admin)
✅ [PASS] Evaluación de Rol: Barbero (barber)
✅ [PASS] Evaluación de Rol: Cliente (user)
✅ [PASS] Catálogo de Servicios accesible
✅ [PASS] Catálogo de Barberos accesible
✅ [PASS] Creación de Cita exitosa
✅ [PASS] Check-in de Cita actualizado a 'in_process'
```

---

## 📋 Cumplimiento de Requerimientos No Funcionales

1. **Agenda pública accesible sin login (solo para reservar):** Disponible en `/reservar`.
2. **Vista de calendario tipo semana/día para el barbero:** Integrada con FullCalendar.js.
3. **Multi-sucursal:** Tabla y modelo `Branch` asignado a barberos y citas.
4. **Diseño oscuro / Barbershop style:** Estilizado en tonos carbón (`#0f172a`) y acentos ámbar (`#d97706`).
5. **Respuesta de agenda < 1 seg (AJAX/Fetch):** Creación y carga asíncrona de datos en tiempo real.