<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Management</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f6fb;
            color: #1e293b;
        }

        header {
            background: #172554;
            color: white;
            padding: 18px 4%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        header h2 { margin: 0; font-size: 22px; }

        .container {
            max-width: 1250px;
            margin: 28px auto;
            padding: 0 20px;
        }

        .panel {
            background: white;
            padding: 22px;
            border-radius: 12px;
            margin-bottom: 22px;
            box-shadow: 0 3px 15px rgba(15, 23, 42, .05);
        }

        h3 { margin: 0 0 18px; }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 15px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            color: #475569;
        }

        input, select {
            width: 100%;
            min-width: 0;
            padding: 11px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            background: white;
            font-size: 14px;
        }

        button {
            padding: 10px 14px;
            border: 0;
            border-radius: 7px;
            cursor: pointer;
            font-weight: 600;
        }

        button:disabled { opacity: .6; cursor: not-allowed; }

        .primary { background: #2563eb; color: white; }
        .secondary { background: #e2e8f0; color: #1e293b; }
        .danger { background: #fee2e2; color: #b91c1c; }
        .logout { background: white; color: #172554; }

        .actions, .row-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .actions { margin-top: 17px; }

        .filters {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr auto;
            gap: 10px;
            margin-bottom: 18px;
        }

        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
        }

        th, td {
            padding: 13px 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }

        th { background: #f8fafc; color: #475569; }

        #message {
            margin: 0 0 18px;
            overflow-wrap: anywhere;
        }

        .error { color: #b91c1c; }
        .success { color: #15803d; }

        .pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 18px;
        }

        #pageInfo {
            font-size: 14px;
            color: #475569;
            text-align: center;
        }

        [hidden] { display: none !important; }

        @media (max-width: 850px) {
            .form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .filters { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 520px) {
            .form-grid, .filters { grid-template-columns: 1fr; }
            header h2 { font-size: 18px; }
            .container { padding: 0 12px; }
            .panel { padding: 16px; }
            .pagination { flex-wrap: wrap; justify-content: center; }
        }
    </style>
</head>
<body>

<header>
    <h2>Employee Management</h2>
    <button type="button" class="logout" id="logoutButton">Logout</button>
</header>

<main class="container">
    <div id="message" role="status" aria-live="polite"></div>

    <section class="panel">
        <h3 id="formTitle">Add New Employee</h3>

        <form id="employeeForm">
            <div class="form-grid">
                <div>
                    <label for="name">Full Name</label>
                    <input id="name" name="name" required maxlength="255">
                </div>

                <div>
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" required maxlength="255">
                </div>

                <div>
                    <label for="department">Department</label>
                    <input id="department" name="department" required maxlength="255">
                </div>

                <div>
                    <label for="salary">Salary</label>
                    <input id="salary" name="salary" type="number"
                           min="0" max="9999999999.99" step="0.01" required>
                </div>

                <div>
                    <label for="joining_date">Joining Date</label>
                    <input id="joining_date" name="joining_date" type="date" required>
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="primary" id="saveButton">
                    Add Employee
                </button>

                <button type="button" class="secondary" id="cancelEdit" hidden>
                    Cancel Edit
                </button>
            </div>
        </form>
    </section>

    <section class="panel">
        <h3>Employee Records</h3>

        <div class="filters">
            <input id="search" placeholder="Search by name" aria-label="Search by name">
            <input id="departmentFilter" placeholder="Department" aria-label="Filter by department">

            <select id="sortBy" aria-label="Sort by">
                <option value="joining_date">Joining Date</option>
                <option value="salary">Salary</option>
            </select>

            <select id="sortOrder" aria-label="Sort order">
                <option value="desc">Descending</option>
                <option value="asc">Ascending</option>
            </select>

            <button type="button" class="primary" id="searchButton">Search</button>
        </div>

        <div class="table-wrap">
            <table id="employeesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Salary</th>
                        <th>Joining Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="employeeTableBody">
                    <tr><td colspan="7">Loading employees...</td></tr>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <button type="button" class="secondary" id="previousButton" disabled>Previous</button>
            <span id="pageInfo">Page 1 of 1</span>
            <button type="button" class="secondary" id="nextButton" disabled>Next</button>
        </div>
    </section>
</main>

<script>
'use strict';

const API = '/api';
let token = sessionStorage.getItem('jwt_token');

const message = document.getElementById('message');
const form = document.getElementById('employeeForm');
const tbody = document.getElementById('employeeTableBody');
const saveButton = document.getElementById('saveButton');

let currentPage = 1;
let lastPage = 1;
let editingId = null;

function showMessage(text, type = '') {
    message.textContent = text;
    message.className = type;
}

function makeCell(value) {
    const cell = document.createElement('td');
    cell.textContent = value ?? '';
    return cell;
}

function makeButton(text, className, handler) {
    const button = document.createElement('button');
    button.type = 'button';
    button.textContent = text;
    button.className = className;
    button.addEventListener('click', handler);
    return button;
}

// Handle JSON responses and report server-side errors clearly.
async function api(path, options = {}) {
    const response = await fetch(API + path, {
        ...options,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            ...(token ? { 'Authorization': 'Bearer ' + token } : {}),
            ...(options.headers || {})
        }
    });

    const raw = await response.text();
    let data = {};

    try {
        data = raw ? JSON.parse(raw) : {};
    } catch {
        console.error('Non-JSON API response:', raw);
        throw new Error(
            `The server returned a non-JSON response (HTTP ${response.status}). Check the Laravel logs.`
        );
    }

    if (response.status === 401) {
        sessionStorage.removeItem('jwt_token');
        token = null;
        window.location.replace('/');
        throw new Error('Session expired. Please log in again.');
    }

    if (!response.ok) {
        const validationErrors = data.errors
            ? Object.values(data.errors).flat().join(' ')
            : '';

        throw new Error(
            validationErrors || data.message || `API request failed: HTTP ${response.status}`
        );
    }

    return data;
}

// Fetch and render paginated employee records.
async function loadEmployees(page = 1) {
    if (!token) {
        window.location.replace('/');
        return;
    }

    tbody.replaceChildren();

    const loadingRow = document.createElement('tr');
    const loadingCell = document.createElement('td');
    loadingCell.colSpan = 7;
    loadingCell.textContent = 'Loading employees...';
    loadingRow.appendChild(loadingCell);
    tbody.appendChild(loadingRow);

    try {
        const params = new URLSearchParams({
            page: String(page),
            per_page: '10',
            search: document.getElementById('search').value.trim(),
            department: document.getElementById('departmentFilter').value.trim(),
            sort_by: document.getElementById('sortBy').value,
            sort_order: document.getElementById('sortOrder').value
        });

        const result = await api('/employees?' + params.toString());

        console.log('GET employees response:', result);

        // Laravel paginate() returns an object with an array in `data`.
        if (!result || !Array.isArray(result.data)) {
            console.error('Unexpected employee-list response:', result);
            throw new Error(
                'Unexpected API response: employee list must contain a data array. Check GET /api/employees.'
            );
        }

        tbody.replaceChildren();

        if (result.data.length === 0) {
            const row = document.createElement('tr');
            const cell = document.createElement('td');
            cell.colSpan = 7;
            cell.textContent = 'No employee records found.';
            row.appendChild(cell);
            tbody.appendChild(row);
        }

        result.data.forEach(employee => {
            const row = document.createElement('tr');

            row.appendChild(makeCell(employee.id));
            row.appendChild(makeCell(employee.name));
            row.appendChild(makeCell(employee.email));
            row.appendChild(makeCell(employee.department));
            row.appendChild(makeCell(employee.salary));
            row.appendChild(makeCell(employee.joining_date));

            const actions = document.createElement('td');
            actions.className = 'row-actions';

            actions.appendChild(makeButton('Edit', 'secondary', () => {
                editEmployee(employee);
            }));

            actions.appendChild(makeButton('Delete', 'danger', () => {
                deleteEmployee(employee.id);
            }));

            row.appendChild(actions);
            tbody.appendChild(row);
        });

        currentPage = result.current_page ?? page;
        lastPage = result.last_page ?? 1;

        document.getElementById('pageInfo').textContent =
            `Page ${currentPage} of ${lastPage} (${result.total ?? result.data.length} employees)`;

        document.getElementById('previousButton').disabled = currentPage <= 1;
        document.getElementById('nextButton').disabled = currentPage >= lastPage;

        showMessage('');

    } catch (error) {
        console.error('Unable to load employees:', error);

        tbody.replaceChildren();

        const row = document.createElement('tr');
        const cell = document.createElement('td');
        cell.colSpan = 7;
        cell.textContent = 'Unable to load employee records.';
        row.appendChild(cell);
        tbody.appendChild(row);

        showMessage(error.message, 'error');
    }
}

function resetForm() {
    editingId = null;
    form.reset();

    document.getElementById('formTitle').textContent = 'Add New Employee';
    saveButton.textContent = 'Add Employee';
    document.getElementById('cancelEdit').hidden = true;
}

function editEmployee(employee) {
    editingId = employee.id;

    document.getElementById('name').value = employee.name ?? '';
    document.getElementById('email').value = employee.email ?? '';
    document.getElementById('department').value = employee.department ?? '';
    document.getElementById('salary').value = employee.salary ?? '';
    document.getElementById('joining_date').value =
        String(employee.joining_date ?? '').slice(0, 10);

    document.getElementById('formTitle').textContent =
        'Edit Employee #' + employee.id;

    saveButton.textContent = 'Update Employee';
    document.getElementById('cancelEdit').hidden = false;
    showMessage('');

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Add or update an employee.
form.addEventListener('submit', async event => {
    event.preventDefault();

    if (!token) {
        window.location.replace('/');
        return;
    }

    saveButton.disabled = true;
    showMessage('');

    const employee = {
        name: document.getElementById('name').value.trim(),
        email: document.getElementById('email').value.trim(),
        department: document.getElementById('department').value.trim(),
        salary: Number(document.getElementById('salary').value),
        joining_date: document.getElementById('joining_date').value
    };

    try {
        const isEditing = editingId !== null;
        const path = isEditing ? '/employees/' + editingId : '/employees';

        const result = await api(path, {
            method: isEditing ? 'PUT' : 'POST',
            body: JSON.stringify(employee)
        });

        console.log('Save employee response:', result);

        // store() returns { message, data: employee }; no array is expected here.
        showMessage(
            result.message || (isEditing
                ? 'Employee updated successfully.'
                : 'Employee added successfully.'),
            'success'
        );

        resetForm();

        if (!isEditing) {
            document.getElementById('search').value = '';
            document.getElementById('departmentFilter').value = '';
            document.getElementById('sortBy').value = 'joining_date';
            document.getElementById('sortOrder').value = 'desc';
        }

        await loadEmployees(1);

    } catch (error) {
        console.error('Unable to save employee:', error);
        showMessage(error.message || 'Unable to save employee.', 'error');
    } finally {
        saveButton.disabled = false;
    }
});

async function deleteEmployee(id) {
    if (!confirm('Are you sure you want to delete employee #' + id + '?')) {
        return;
    }

    try {
        const result = await api('/employees/' + id, {
            method: 'DELETE'
        });

        showMessage(result.message || 'Employee deleted successfully.', 'success');

        await loadEmployees(currentPage);

        if (currentPage > lastPage && currentPage > 1) {
            await loadEmployees(currentPage - 1);
        }
    } catch (error) {
        console.error('Unable to delete employee:', error);
        showMessage(error.message, 'error');
    }
}

// Search and filtering.
document.getElementById('searchButton').addEventListener('click', () => {
    loadEmployees(1);
});

['search', 'departmentFilter'].forEach(id => {
    document.getElementById(id).addEventListener('keydown', event => {
        if (event.key === 'Enter') {
            event.preventDefault();
            loadEmployees(1);
        }
    });
});

document.getElementById('sortBy').addEventListener('change', () => loadEmployees(1));
document.getElementById('sortOrder').addEventListener('change', () => loadEmployees(1));

// Pagination.
document.getElementById('previousButton').addEventListener('click', () => {
    if (currentPage > 1) loadEmployees(currentPage - 1);
});

document.getElementById('nextButton').addEventListener('click', () => {
    if (currentPage < lastPage) loadEmployees(currentPage + 1);
});

// Cancel editing.
document.getElementById('cancelEdit').addEventListener('click', () => {
    resetForm();
    showMessage('');
});

// Logout.
document.getElementById('logoutButton').addEventListener('click', async () => {
    try {
        if (token) {
            await api('/logout', { method: 'POST' });
        }
    } catch (error) {
        console.warn('Logout request failed:', error.message);
    } finally {
        sessionStorage.removeItem('jwt_token');
        token = null;
        window.location.replace('/');
    }
});

// Start the page.
if (token) {
    loadEmployees();
} else {
    window.location.replace('/');
}
</script>

</body>
</html>
