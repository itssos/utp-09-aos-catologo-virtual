<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Librería Jesús Mi Amigo</title>
    <!-- Google Fonts + FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        rel="stylesheet"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Estilos globales -->
    <link rel="stylesheet" href="/assets/css/global.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/assets/css/header.css?v=<?= time() ?>">
</head>

<body>
    <header class="header">
        <div class="container">
            <a href="<?= route('home') ?>" class="logo">Librería Jesús Mi Amigo</a>

            <div class="menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <nav>
                <a href="<?= route('home') ?>">Inicio</a>

                <?php if (!isAuth()): ?>
                    <a href="<?= route('login') ?>">Intranet</a>
                <?php endif; ?>

                <?php if (can('create_user')): ?>
                    <a href="<?= route('register') ?>">Registrar usuario</a>
                <?php endif; ?>

                <?php if (can('view_product')): ?>
                    <a href="<?= route('product_store') ?>">Catalogo</a>
                <?php endif; ?>

                <?php if (isAuth()): ?>
                    <a href="<?= route('logout') ?>">Salir</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggle = document.querySelector('.menu-toggle');
            const nav = document.querySelector('header nav');
            toggle.addEventListener('click', () => {
                nav.classList.toggle('open');
                toggle.classList.toggle('active');
            });
        });
    </script>


    <main>