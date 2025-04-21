<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
    main {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 90vh;
    }

    .error-container {
        text-align: center;
        max-width: 600px;
        background-color: #fff8e7;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .error-code {
        font-size: 96px;
        font-weight: bold;
        color: #a65e2e;
    }

    .error-message {
        font-size: 24px;
        margin: 20px 0;
        font-style: italic;
    }

    .book-icon {
        font-size: 50px;
        margin-bottom: 10px;
        color: #a65e2e;
    }

    .home-button {
        display: inline-block;
        margin-top: 30px;
        padding: 12px 25px;
        background-color: #a65e2e;
        color: #fff8e7;
        text-decoration: none;
        border-radius: 8px;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }

    .home-button:hover {
        background-color: #86451f;
    }
</style>

<div class="error-container">
    <div class="book-icon">📖</div>
    <div class="error-code">404</div>
    <div class="error-message">¡Vaya! No encontramos esta página en nuestra web.<br>Tal vez se ha perdido...</div>
    <a href="<?= route('home') ?>" class="home-button">Volver al inicio</a>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>