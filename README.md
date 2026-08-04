# BarberPro — Sistema Integral de Gestión de Barberías

![Version](https://img.shields.io/badge/version-1.0.0-blue)
[![Docker](https://img.shields.io/badge/Docker-Supported-blue?logo=docker)](https://www.docker.com/)
[![Vercel](https://img.shields.io/badge/Vercel-Deployment-black?logo=vercel)](https://vercel.com/)
[![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel)](https://laravel.com/)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

**BarberPro** es un sistema web integral y moderno para la gestión de barberías con capacidades multi-sucursal, control de comisiones, reserva online pública, confirmaciones por WhatsApp, y panel administrativo completo.

---

## 📋 Tabla de Contenidos

- [Características](#características)
- [Stack Tecnológico](#stack-tecnológico)
- [Requisitos](#requisitos)
- [Instalación Local (Docker)](#instalación-local-docker)
- [Despliegue en Producción (Vercel)](#despliegue-en-producción-vercel)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Roles de Usuario](#roles-de-usuario)
- [Variables de Entorno](#variables-de-entorno)
- [API & Rutas](#api--rutas)
- [Solución de Problemas](#solución-de-problemas)
- [Contribuir](#contribuir)

---

## ✨ Características

### Para Clientes
- ✅ **Reserva Online Sin Login** — Sistema de booking público sin necesidad de cuenta
- ✅ **Selección de Barbero** — Elige barbero preferido antes de reservar
- ✅ **Múltiples Sucursales** — Selecciona sucursal al agendar
- ✅ **Confirmación por WhatsApp** — Notificaciones automáticas de citas
- ✅ **Historial de Citas** — Panel de cliente con citas pasadas y futuras

### Para Barberos
- ✅ **Panel Personal** — Dashboard con citas del día
- ✅ **Check-in Rápido** — Marcar citas como "en proceso"
- ✅ **Comisiones Automáticas** — 40% sobre servicios completados
- ✅ **Propinas Registradas** — Control de propinas por cita
- ✅ **Historial de Clientes** — Registro de clientes atendidos
- ✅ **Exportar CSV** — Descargar reporte de comisiones

### Para Administradores
- ✅ **Gestión Multi-Sucursal** — Crear y administrar sucursales
- ✅ **Control de Usuarios** — Roles: Admin, Barbero, Cliente, Encargado
- ✅ **Gestión de Servicios** — Crear servicios con precios dinámicos
- ✅ **Gestión de Clientes** — Base de datos centralizada de clientes
- ✅ **Reportes & Analytics** — Métricas de ingresos y comisiones
- ✅ **Módulo de Cobros** — Registro de pagos en caja
- ✅ **Exportación de Datos** — Reportes en Excel/CSV

---

## 🛠 Stack Tecnológico

| Capa | Tecnología |
|------|------------|
| **Backend** | Laravel 11 (PHP 8.2+) |
| **Frontend** | Blade + Tailwind CSS + Alpine.js |
| **Database** | MySQL 8 / PlanetScale |
| **Local Development** | Docker Compose (Laravel + MySQL + phpMyAdmin) |
| **Production** | Vercel Serverless Functions |
| **Assets** | Vite + Laravel Vite Plugin |
| **Autenticación** | Sanctum (API) + Session (Web) |
| **Cola de Trabajos** | Redis Queue (opcional) |
| **Notificaciones** | WhatsApp API (Twilio/Wapi) |

---

## 📦 Requisitos

### Desarrollo Local
- Docker Desktop (v20.10+)
- Docker Compose (v2.0+)
- Git

### Producción
- Cuenta Vercel
- Base de datos MySQL remota (PlanetScale, Railway, AWS RDS, etc.)
- Credenciales WhatsApp API

---

## 🚀 Instalación Local (Docker)

### Paso 1: Clonar el Repositorio

```bash
git clone https://github.com/DarlaSolis/barber-shop-project-6b.git
cd barber-shop-project-6b
```

### Paso 2: Configurar Variables de Entorno

```bash
cp .env.example .env
```

Edita `.env` con tus credenciales locales:

```env
APP_NAME=BarberPro
APP_ENV=local
APP_KEY=base64:... # Se genera automáticamente
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=barbershop
DB_USERNAME=barbershop
DB_PASSWORD=secret

MAIL_DRIVER=log
QUEUE_CONNECTION=sync

# WhatsApp (opcional)
WAPI_TOKEN=tu_token_aqui
```

### Paso 3: Levantar Contenedores

```bash
docker-compose up -d --build
```

**Contenedores que se crean:**
- `app` — Servidor Laravel (puerto 8000)
- `mysql` — Base de datos MySQL 8 (puerto 3306)
- `phpmyadmin` — Interfaz gráfica MySQL (puerto 8080)

### Paso 4: Instalar Dependencias & Migrar

```bash
# Instalar dependencias PHP
docker-compose exec app composer install

# Generar APP_KEY
docker-compose exec app php artisan key:generate

# Ejecutar migraciones
docker-compose exec app php artisan migrate

# (Opcional) Cargar datos de prueba
docker-compose exec app php artisan db:seed
```

### Acceso a la Aplicación

| Servicio | URL | Credenciales |
|----------|-----|--------------|
| **Web App** | http://localhost:8000 | — |
| **Reserva Pública** | http://localhost:8000 | — |
| **phpMyAdmin** | http://localhost:8080 | root / root |

### Comandos Útiles de Docker

```bash
# Ver logs en tiempo real
docker-compose logs -f app

# Acceder a la shell del contenedor
docker-compose exec app bash

# Ejecutar Artisan
docker-compose exec app php artisan tinker

# Detener contenedores
docker-compose down

# Reconstruir contenedores (si cambió docker-compose.yml)
docker-compose up -d --build
```

---

## ⚡ Despliegue en Producción (Vercel)

### Arquitectura en Vercel

BarberPro usa **Serverless Functions** de Vercel. La configuración está en `vercel.json`:

```json
{
  "version": 2,
  "framework": null,
  "functions": {
    "api/index.php": {
      "runtime": "vercel-php@0.7.3"
    }
  },
  "routes": [
    { "src": "/build/(.*)", "dest": "/public/build/$1" },
    { "src": "/(css|js|images|fonts)/(.*)", "dest": "/public/$1/$2" },
    { "src": "/(.*)", "dest": "/api/index.php" }
  ]
}
```

Todas las peticiones se enrutan a `api/index.php` que ejecuta Laravel.

### Paso 1: Conectar Repositorio en Vercel

1. Ve a [vercel.com](https://vercel.com) y crea una cuenta (gratis)
2. Haz clic en "New Project"
3. Importa este repositorio desde GitHub
4. Selecciona el equipo y proyecto

### Paso 2: Configurar Variables de Entorno

En el dashboard de Vercel, ve a **Settings → Environment Variables** y agrega:

```env
APP_NAME=BarberPro
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.vercel.app

# Seguridad
APP_KEY=base64:YOUR_APP_KEY_HERE  # Genera con: php artisan key:generate --show

# Base de Datos (remota)
DB_CONNECTION=mysql
DB_HOST=your-mysql-host.com
DB_PORT=3306
DB_DATABASE=barbershop_prod
DB_USERNAME=db_user
DB_PASSWORD=db_password_secure

# Mail (para notificaciones)
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=tu_email@mailtrap.io
MAIL_PASSWORD=tu_password
MAIL_FROM_ADDRESS=noreply@barberpro.com

# WhatsApp API
WAPI_TOKEN=tu_token_wapi

# Redis (si usas queue)
REDIS_URL=redis://your-redis-host:6379

# Session
SESSION_DRIVER=cookie
SESSION_LIFETIME=120
```

### Paso 3: Preparar la Base de Datos Remota

Opciones recomendadas:

**PlanetScale (MySQL Serverless)**
```bash
# Crear BD en PlanetScale y copiar credenciales
# Costo: Gratis para desarrollo
```

**Railway (all-in-one)**
```bash
# Hosting + MySQL en un mismo dashboard
# Costo: Pago por uso
```

**AWS RDS**
```bash
# Base de datos gestionada de Amazon
# Costo: Variable según uso
```

### Paso 4: Ejecutar Migraciones en Producción

```bash
# Por SSH en Vercel (via CLI)
vercel env pull  # Descargar variables locales
php artisan migrate --env=production
php artisan db:seed  # Opcional
```

O ejecutar directamente contra la BD remota:
```bash
php artisan migrate --database=production
```

### Paso 5: Desplegar

```bash
# Opción 1: Push a main branch
git push origin main
# Vercel se despliega automáticamente

# Opción 2: Desplegar manualmente desde CLI
vercel --prod
```

### Acceso a Producción

```
https://barber-shop-project-6b.vercel.app
```

---

## 📁 Estructura del Proyecto

```
barber-shop-project-6b/
├── app/
│   ├── Models/              # Modelos Eloquent
│   │   ├── User.php
│   │   ├── Appointment.php
│   │   ├── Client.php
│   │   ├── Barber.php
│   │   ├── Service.php
│   │   └── Branch.php
│   ├── Http/
│   │   ├── Controllers/     # Controladores
│   │   ├── Requests/        # Form Request validation
│   │   └── Middleware/
│   ├── Services/            # Servicios de lógica
│   │   └── WapiService.php  # Integraciones WhatsApp
│   └── Jobs/                # Jobs en cola
│       └── SendAppointmentReminder.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── admin.blade.php       # Sidebar responsivo + header
│   │   │   ├── public.blade.php      # Layout público
│   │   │   └── app.blade.php
│   │   ├── appointments/
│   │   ├── barber/
│   │   ├── clientes/
│   │   ├── dashboard/
│   │   └── auth/
│   ├── css/
│   │   └── app.css          # Tailwind CSS
│   └── js/
│       └── app.js
├── routes/
│   ├── web.php              # Rutas web
│   ├── auth.php
│   └── api.php              # API endpoints (JSON)
├── database/
│   ├── migrations/          # Schema de BD
│   ├── factories/           # Data factories
│   └── seeders/
├── config/
│   ├── app.php
│   ├── database.php
│   └── services.php
├── docker-compose.yml       # Orquestación local
├── vercel.json              # Config Vercel
├── vite.config.js           # Compilador assets
└── .env.example             # Template de variables
```

---

## 👥 Roles de Usuario

### 1. **Administrador General** (`role = 'admin'`)
- Acceso total al sistema
- Crear/editar sucursales, barberos, servicios, clientes
- Ver reportes de ingresos y comisiones
- Gestionar usuarios y permisos
- Exportar datos

### 2. **Encargado de Sucursal** (`role = 'encargado'`)
- Gestionar barberos de su sucursal
- Ver citas de su sucursal
- Registrar cobros diarios
- Ver reportes limitados a su sucursal

### 3. **Barbero** (`role = 'barber'`)
- Ver panel personal con citas del día
- Marcar citas como "en proceso" (check-in)
- Completar citas
- Ver historial de clientes
- Ver comisiones ganadas
- Exportar reporte personal

### 4. **Cliente** (`role = 'customer'`)
- Ver citas personales
- Reservar nuevas citas
- Ver historial de citas
- Editar información de perfil

### 5. **Público** (sin login)
- Acceso solo a la página de reserva online
- Agendar cita sin crear cuenta
- Recibir confirmación por WhatsApp

---

## 🔐 Variables de Entorno

### Desarrollo (Local)

```env
APP_NAME=BarberPro
APP_ENV=local
APP_KEY=base64:... # php artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=barbershop
DB_USERNAME=barbershop
DB_PASSWORD=secret

MAIL_DRIVER=log
QUEUE_CONNECTION=sync
SESSION_DRIVER=cookie

CACHE_DRIVER=file
```

### Producción (Vercel)

```env
APP_NAME=BarberPro
APP_ENV=production
APP_DEBUG=false
APP_URL=https://barber-shop-project-6b.vercel.app

APP_KEY=base64:... # NO cambiar en producción
DB_CONNECTION=mysql
DB_HOST=db.planeta-scale.com  # Tu host remoto
DB_PORT=3306
DB_DATABASE=barbershop_prod
DB_USERNAME=admin
DB_PASSWORD=very_strong_password

MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465

SESSION_DRIVER=cookie  # Para Vercel Serverless
CACHE_DRIVER=file

# WhatsApp
WAPI_TOKEN=token_from_wapi
```

---

## 🔗 API & Rutas

### Rutas Web Principales

| Método | Ruta | Descripción |
|--------|------|------------|
| GET | `/` | Home / Dashboard |
| GET | `/reservar` | Formulario de reserva pública |
| POST | `/api/appointments` | Crear cita (JSON) |
| POST | `/api/clients/quick-store` | Registrar cliente rápido |
| GET | `/barber` | Panel del barbero |
| PUT | `/barber/appointments/{id}/status` | Cambiar estado de cita |
| GET | `/barber/export` | Descargar CSV de comisiones |
| GET | `/clientes` | Gestión de clientes (admin) |
| POST | `/clientes` | Crear cliente |
| GET | `/reportes` | Reportes (admin) |

### Endpoints API (JSON)

```bash
# Crear cita
POST /api/appointments
Content-Type: application/json
{
  "client_id": 1,
  "branch_id": 1,
  "barber_id": 1,
  "service_id": 1,
  "date": "2024-08-20",
  "hour": 14,
  "minute": 30,
  "period": "PM",
  "payment_method": "Efectivo"
}

# Registro rápido de cliente
POST /api/clients/quick-store
{
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "phone": "5551234567"
}

# Actualizar estado de cita
PUT /barber/appointments/{appointmentId}/status
{
  "status": "completed",
  "payment_method": "Tarjeta",
  "tip": 50
}
```

---

## 🐛 Solución de Problemas

### **Problema: Docker no levanta correctamente**
```bash
# Solución: Reconstruir e iniciar de nuevo
docker-compose down -v
docker-compose up -d --build
```

### **Problema: Migraciones fallan**
```bash
# Verificar logs
docker-compose logs mysql

# Conectarse directamente a MySQL
docker-compose exec mysql mysql -u root -p
```

### **Problema: Assets (CSS/JS) no cargan en desarrollo**
```bash
# Compilar Vite en modo watch
docker-compose exec app npm run dev
```

### **Problema: Errores 500 en Vercel**
```bash
# Ver logs detallados
vercel logs --follow

# Verificar variables de entorno
vercel env list
```

### **Problema: WhatsApp no envía mensajes**
- Verificar `WAPI_TOKEN` correcto
- Comprobar que el número destino sea válido
- Revisar logs: `docker-compose logs app | grep wapi`

---

## 🤝 Contribuir

Las contribuciones son bienvenidas. Para grandes cambios:

1. Fork el repositorio
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

### Estándares de Código
- PSR-12 para PHP
- Blade limpio, sin lógica compleja
- Tailwind CSS para estilos (sin CSS personalizado innecesario)
- Nombres descriptivos en inglés para variables/métodos

---

## 📄 Licencia

Este proyecto está bajo la licencia MIT. Ver archivo [LICENSE](LICENSE) para más detalles.

---

## 📞 Contacto & Soporte

- **Email:** support@barberpro.com
- **Issues:** [GitHub Issues](https://github.com/DarlaSolis/barber-shop-project-6b/issues)
- **Documentación:** [Wiki](https://github.com/DarlaSolis/barber-shop-project-6b/wiki)

---

**Hecho con ❤️ para barberías del futuro** 💈

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
