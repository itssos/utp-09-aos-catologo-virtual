<?php include __DIR__ . '/../layouts/header.php'; ?>


<div class="login-card">
    <h2><i class="fas fa-book-open"></i> Intranet Librería</h2>
    <p id="error-message"><?= htmlspecialchars($_GET['error'] ?? '') ?></p>

    <form id="login-form" autocomplete="off">
        <label>Usuario:
            <input type="text" name="username" placeholder="Tu usuario" required>
        </label>
        <label>Contraseña:
            <input type="password" name="password" placeholder="••••••••" required>
        </label>
        <button type="submit">Ingresar</button>
    </form>
</div>


<?php include __DIR__ . '/../layouts/footer.php'; ?>

<script>
    document.getElementById('login-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const errorEl = document.getElementById('error-message');
        errorEl.textContent = '';

        const form = e.target;
        const data = {
            username: form.username.value,
            password: form.password.value
        };

        try {
            const res = await fetch('http://localhost:8001/api/login', {
                method: 'POST',
                credentials: 'include', // para que almacene la cookie
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (!res.ok) {
                // HTTP >=400
                const text = await res.text();
                throw new Error(text || res.statusText);
            }

            const json = await res.json();
            if (json.status !== 'success') {
                throw new Error(json.message || 'Error de autenticación');
            }

            window.location.href = '/admin/productos';
        } catch (err) {
            console.error(err);
            errorEl.textContent = err.message;
        }
    });
</script>

<style>
    /* --- Card Container --- */
    .login-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(6px);
        padding: 2rem 2.5rem;
        border-radius: 1.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
        text-align: center;
    }

    .login-card h2 {
        margin-bottom: 1.5rem;
        font-family: 'Merriweather', serif;
        color: var(--primary);
        font-size: 1.75rem;
    }

    .login-card label {
        display: block;
        text-align: left;
        font-weight: 600;
        margin-top: 1rem;
        color: var(--primary);
    }

    .login-card input {
        width: 100%;
        padding: 0.8rem 1rem;
        margin-top: 0.4rem;
        border: 1px solid #ddd;
        border-radius: 0.8rem;
        font-size: 1rem;
        outline: none;
        transition: border-color 0.3s;
    }

    .login-card input:focus {
        border-color: var(--accent);
    }

    .login-card button {
        margin-top: 1.8rem;
        width: 100%;
        padding: 0.9rem;
        border: none;
        border-radius: 0.9rem;
        background: var(--primary-color);
        color: #fff;
        font-size: 1.05rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s, transform 0.2s;
    }

    .login-card button:hover {
        background: var(--accent-color);
        transform: translateY(-2px);
    }

    #error-message {
        margin-top: 0.5rem;
        color: #e74c3c;
        font-weight: 500;
    }

    main {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 90vh;
    }
</style>