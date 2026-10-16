<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 35px;
            background: #f4f6f9;
        }
        .container {
            background: white;
            padding: 25px;
            border-radius: 10px;
        }
        input, button {
            padding: 10px;  
            margin: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th { background: #e9eef5; }
        #message { margin: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Employee Management</h2>

    <form id="loginForm">
        <h3>Login</h3>
        <input type="email" id="email" placeholder="Email" required>
        <input type="password" id="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <hr>

    <form id="employeeForm">
        <h3>Add Employee</h3>
        <input id="name" placeholder="Name" required>
        <input id="empEmail" type="email" placeholder="Email" required>
        <input id="department" placeholder="Department" required>
        <input id="salary" type="number" step="0.01"
               min="0" placeholder="Salary" required>
        <input id="joiningDate" type="date" required>
        <button type="submit">Add Employee</button>
    </form>

    <div id="message"></div>

    <h3>Employee Records</h3>
    <input id="search" placeholder="Search employee name">
    <button onclick="loadEmployees()">Search</button>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Department</th>
            <th>Salary</th>
            <th>Joining Date</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody id="employeeRows"></tbody>
    </table>
</div>

<script>
let token = '';

const message = (text) => {
    document.getElementById('message').textContent = text;
};

async function api(url, options = {}) {
    const response = await fetch('/api' + url, {
        ...options,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            ...(token ? { 'Authorization': 'Bearer ' + token } : {}),
            ...(options.headers || {})
        }
    });

    const data = await response.json();

    if (!response.ok) {
        throw new Error(
            data.message || JSON.stringify(data.errors || data)
        );
    }

    return data;
}

document.getElementById('loginForm').onsubmit = async (event) => {
    event.preventDefault();

    try {
        const data = await api('/login', {
            method: 'POST',
            body: JSON.stringify({
                email: document.getElementById('email').value,
                password: document.getElementById('password').value
            })
        });

        token = data.access_token;
        message('Login successful');
        await loadEmployees();
    } catch (error) {
        message(error.message);
    }
};

async function loadEmployees() {
    try {
        const search = document.getElementById('search').value;
        const data = await api(
            '/employees?search=' + encodeURIComponent(search)
        );

        const rows = document.getElementById('employeeRows');
        rows.replaceChildren();

        data.data.forEach(employee => {
            const row = document.createElement('tr');

            [
                employee.id,
                employee.name,
                employee.email,
                employee.department,
                employee.salary,
                employee.joining_date
            ].forEach(value => {
                const cell = document.createElement('td');
                cell.textContent = value ?? '';
                row.appendChild(cell);
            });

            const action = document.createElement('td');
            const button = document.createElement('button');
            button.textContent = 'Delete';

            button.onclick = async () => {
                if (!confirm('Delete this employee?')) return;

                try {
                    await api('/employees/' + employee.id, {
                        method: 'DELETE'
                    });
                    await loadEmployees();
                    message('Employee deleted');
                } catch (error) {
                    message(error.message);
                }
            };

            action.appendChild(button);
            row.appendChild(action);
            rows.appendChild(row);
        });
    } catch (error) {
        message(error.message);
    }
}

document.getElementById('employeeForm').onsubmit = async (event) => {
    event.preventDefault();

    try {
        await api('/employees', {
            method: 'POST',
            body: JSON.stringify({
                name: document.getElementById('name').value,
                email: document.getElementById('empEmail').value,
                department: document.getElementById('department').value,
                salary: Number(document.getElementById('salary').value),
                joining_date: document.getElementById('joiningDate').value
            })
        });

        message('Employee added successfully');
        event.target.reset();
        await loadEmployees();
    } catch (error) {
        message(error.message);
    }
};
</script>
</body>
</html>
