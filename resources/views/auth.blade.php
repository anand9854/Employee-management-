<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Portal | Authentication</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: #f3f6fb;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #1e293b;
        }

        .card {
            background: white;
            width: 100%;
            max-width: 430px;
            padding: 32px;
            border-radius: 14px;
            box-shadow: 0 8px 35px rgba(15, 23, 42, .08);
        }

        h1 { margin: 0 0 8px; font-size: 25px; }
        .subtitle { color: #64748b; margin-bottom: 24px; }

        .tabs { display: flex; gap: 8px; margin-bottom: 22px; }

        .tabs button {
            flex: 1;
            background: #e9eef5;
            color: #334155;
        }

        .tabs button.active {
            background: #2563eb;
            color: white;
        }

        label {
            display: block;
            font-size: 14px;
            margin: 14px 0 7px;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 15px;
        }

        button {
            border: 0;
            border-radius: 7px;
            padding: 12px 15px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
        }

        .submit {
            width: 100%;
            margin-top: 22px;
            background: #2563eb;
            color: white;
        }

        button:disabled { opacity: .6; cursor: wait; }

        .message {
            margin-top: 16px;
            font-size: 14px;
            overflow-wrap: anywhere;
        }

        .error { color: #b91c1c; }
        .success { color: #15803d; }
        [hidden] { display: none !important; }
    </style>
</head>
<body>
<div class="card">
    <h1>Employee Portal</h1>
    <p class="subtitle">Sign in to manage employee records.</p>

    <div class="tabs">
        <button type="button" id="loginTab" class="active">
            Login
        </button>
        <button type="button" id="registerTab">
            Register
        </button>
    </div>

    <form id="loginForm">
        <label for="loginEmail">Email</label>
        <input id="loginEmail" type="email" required autocomplete="email">

        <label for="loginPassword">Password</label>
        <input id="loginPassword" type="password"
               required autocomplete="current-password">

        <button class="submit" type="submit">Login</button>
    </form>

    <form id="registerForm" hidden>
        <label for="registerName">Full name</label>
        <input id="registerName" required maxlength="255"
               autocomplete="name">

        <label for="registerEmail">Email</label>
        <input id="registerEmail" type="email" required
               autocomplete="email">

        <label for="registerPassword">Password</label>
        <input id="registerPassword" type="password"
               required minlength="8" autocomplete="new-password">

        <label for="confirmPassword">Confirm password</label>
        <input id="confirmPassword" type="password"
               required minlength="8" autocomplete="new-password">

        <button class="submit" type="submit">Create account</button>
    </form>

    <div id="message" class="message" role="status"></div>
</div>

<script>
const API = '/api';

const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');
const loginTab = document.getElementById('loginTab');
const registerTab = document.getElementById('registerTab');
const message = document.getElementById('message');

if (sessionStorage.getItem('jwt_token')) {
    window.location.replace('/employees');
}

function showMessage(text, type = 'error') {
    message.textContent = text;
    message.className = 'message ' + type;
}

function showTab(tab) {
    const isLogin = tab === 'login';

    loginForm.hidden = !isLogin;
    registerForm.hidden = isLogin;
    loginTab.classList.toggle('active', isLogin);
    registerTab.classList.toggle('active', !isLogin);
    showMessage('');
}

loginTab.addEventListener('click', () => showTab('login'));
registerTab.addEventListener('click', () => showTab('register'));

async function sendRequest(path, body) {
    const response = await fetch(API + path, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(body)
    });

    const data = await response.json();

    if (!response.ok) {
        const errors = data.errors
            ? Object.values(data.errors).flat().join(' ')
            : data.message;

        throw new Error(errors || 'Request failed.');
    }

    return data;
}

loginForm.addEventListener('submit', async event => {
    event.preventDefault();

    const button = loginForm.querySelector('button[type="submit"]');
    button.disabled = true;
    showMessage('Signing in...', 'success');

    try {
        const data = await sendRequest('/login', {
            email: document.getElementById('loginEmail').value,
            password: document.getElementById('loginPassword').value
        });

        if (!data.access_token) {
            throw new Error('The API did not return an access token.');
        }

        sessionStorage.setItem('jwt_token', data.access_token);
        window.location.assign('/employees');
    } catch (error) {
        showMessage(error.message);
    } finally {
        button.disabled = false;
    }
});

registerForm.addEventListener('submit', async event => {
    event.preventDefault();

    const password = document.getElementById('registerPassword').value;
    const confirmation = document.getElementById('confirmPassword').value;

    if (password !== confirmation) {
        showMessage('Passwords do not match.');
        return;
    }

    const button = registerForm.querySelector('button[type="submit"]');
    button.disabled = true;
    showMessage('Creating your account...', 'success');

    try {
        const data = await sendRequest('/register', {
            name: document.getElementById('registerName').value,
            email: document.getElementById('registerEmail').value,
            password: password,
            password_confirmation: confirmation
        });

        if (!data.access_token) {
            showTab('login');
            document.getElementById('loginEmail').value =
                document.getElementById('registerEmail').value;

            showMessage('Account created. Please log in.', 'success');
            return;
        }

        sessionStorage.setItem('jwt_token', data.access_token);
        window.location.assign('/employees');
    } catch (error) {
        showMessage(error.message);
    } finally {
        button.disabled = false;
    }
});
</script>
</body>
</html>
