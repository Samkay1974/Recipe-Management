// Fetch user data from the PHP API and populate the table
document.addEventListener("DOMContentLoaded", () => {
    const userTable = document.getElementById("userTable");

    // Fetch data from the server
    fetch("../actions/fetch_users.php")
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                data.forEach(user => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${user.user_id}</td>
                        <td>${user.fname}</td>
                        <td>${user.lname}</td>
                        <td>${user.email}</td>
                        <td>${user.role === "1" ? "Super Admin" : "Regular Admin"}</td>
                        <td>${user.created_at}</td>
                        <td>
                            <a href="../actions/view_user.php?id=${user.user_id}" class="view">View</a>
                            <a href="../actions/update_user.php?id=${user.user_id}" class="update">Update</a>
                            <a href="../actions/delete_user.php?id=${user.user_id}" class="remove" onclick="return confirm('Are you sure you want to delete this user?')">Remove</a>
                        </td>
                    `;
                    userTable.appendChild(row);
                });
            } else {
                userTable.innerHTML = `<tr><td colspan="7">No users found</td></tr>`;
            }
        })
        .catch(error => {
            console.error("Error fetching user data:", error);
        });
});
