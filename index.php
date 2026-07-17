<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Methods Task</title>
    <style>
        body { font-family: sans-serif; margin: 40px auto; max-width: 600px; text-align: center; }
        .form-inline { margin-bottom: 25px; }
        .form-inline input { padding: 6px; margin: 5px; border: 1px solid gray; border-radius: 4px; width: 130px; }
        .form-inline button { padding: 6px 14px; background: green; color: white; border: none; border-radius: 4px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid gray; padding: 10px; }
        th { background: lightgray; }
        .toggle-btn { padding: 4px 10px; cursor: pointer; background: white; border: 1px solid gray; border-radius: 4px; }
    </style>
</head>
<body>
    <h2>Adding new user</h2>
    <form id="userForm" class="form-inline">
        <label>Name:</label>
        <input type="text" id="name" required placeholder="name">
        <label>Age:</label>
        <input type="number" id="age" required placeholder="age">
        <button type="submit">Submit</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Age</th>
                <th>Status</th>
                <th>Action</th>
                <th>ID</th>
            </tr>
        </thead>
        <tbody id="userTableBody"></tbody>
    </table>
    <script>
        document.addEventListener("DOMContentLoaded", fetchUsers);

        function fetchUsers() {
            fetch('api.php?action=fetch')
                .then(res => res.json())
                .then(data => {
                    const tbody = document.getElementById('userTableBody');
                    tbody.innerHTML = '';
                    data.forEach(user => {
                        const row = `
                            <tr>
                                <td>${user.name}</td>
                                <td>${user.age}</td>
                                <td id="status-${user.id}">${user.status}</td>
                                <td><button class="toggle-btn" onclick="toggleStatus(${user.id})">Toggle</button></td>
                                <td>${user.id}</td>
                            </tr>
                        `;
                        tbody.insertAdjacentHTML('beforeend', row);
                    });
                });
        }

        document.getElementById('userForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('name').value;
            const age = document.getElementById('age').value;
            fetch('api.php?action=add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, age })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('userForm').reset();
                    fetchUsers();
                }
            });
        });

        function toggleStatus(id) {
            fetch('api.php?action=toggle', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`status-${id}`).innerText = data.new_status;
                }
            });
        }
    </script>
</body>
</html>