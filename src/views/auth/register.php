<?php include __DIR__ . '/../layouts/header.php'; ?>

<h2>Registro de Usuario</h2>

<?php if (!empty($_GET['error'])): ?>
    <p style="color: red;"><?= htmlspecialchars($_GET['error']); ?></p>
<?php endif; ?>

<?php if (!empty($_GET['success'])): ?>
    <p style="color: green;"><?= htmlspecialchars($_GET['success']); ?></p>
<?php endif; ?>

<form action="/register" method="post">
    <div>
        <label for="username">Usuario:</label><br>
        <input type="text" id="username" name="username">
    </div>
    <br>
    <div>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email">
    </div>
    <br>
    <div>
        <label for="password">Contraseña:</label><br>
        <input type="password" id="password" name="password" >
    </div>
    <br>
    <button type="submit">Registrarse</button>
</form>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
