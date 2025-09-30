# Ordenes

Este proyecto utiliza **Laravel** (backend) y **Vue.js** (frontend).

## Prerrequisitos

- PHP >= 8.2
- Composer
- Node.js y npm
- MySQL o base de datos compatible

## Instalación

1. **Clona el repositorio**
    ```bash
    git clone <repo-url>
    cd ordenes
    ```

2. **Instala las dependencias de Laravel**
    ```bash
    composer install
    ```

3. **Instala las dependencias de Vue.js**
    ```bash
    npm install
    ```

4. **Copia y configura el archivo de entorno**
    ```bash
    cp .env.example .env
    # Edita .env con tus credenciales de base de datos
    ```

5. **Genera la clave de la aplicación**
    ```bash
    php artisan key:generate
    ```

## Compilar el Frontend

```bash
npm run build
```

## Ejecutar Migraciones y Seeders

```bash
php artisan migrate --seed
```

## Iniciar los servidores de desarrollo

- **Laravel**
  ```bash
  php artisan serve
  ```
- **Vue.js (si usas Vite)**
  ```bash
  npm run dev
  ```
