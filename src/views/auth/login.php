<?php include __DIR__ . '/../layouts/header.php'; ?>

<h2>Login</h2>
<?php if (!empty($_GET['error'])): ?>
    <p style="color:red;"><?= htmlspecialchars($_GET['error']) ?></p>
<?php endif; ?>

<form action="/login" method="post">
    <label>Usuario:<br>
        <input type="text" name="username" required>
    </label><br><br>
    <label>Contraseña:<br>
        <input type="password" name="password" required>
    </label><br><br>
    <button type="submit">Ingresar</button>
</form>

<?php include __DIR__ . '/../layouts/footer.php'; ?>