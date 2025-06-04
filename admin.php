<?php
include 'db_connect.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['team_id'], $_POST['status'])) {
    $stmt = $conn->prepare("UPDATE teams SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $_POST['status'], $_POST['team_id']);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch all teams
$teamsData = $conn->query("SELECT * FROM teams ORDER BY id DESC");
$teams = [];
while ($row = $teamsData->fetch_assoc()) {
    $teams[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard - WebCraft 2025</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    function searchTable() {
      const input = document.getElementById("searchInput").value.toLowerCase();
      const rows = document.querySelectorAll("#teamsTable tbody tr");
      rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(input) ? "" : "none";
      });
    }
  </script>
</head>
<body class="bg-gradient-to-br from-gray-100 to-gray-200 min-h-screen p-10 font-sans text-gray-800">

  <h1 class="text-4xl font-bold mb-6 text-center drop-shadow text-gray-900">🌟 WebCraft 2025 - Admin Dashboard</h1>

  <!-- Search Bar -->
  <div class="mb-6 text-center">
    <input id="searchInput" onkeyup="searchTable()" type="text" placeholder="Search by name, email, or repo..." class="px-4 py-2 border rounded-lg w-1/2 shadow focus:outline-none focus:ring focus:border-blue-400" />
  </div>

  <!-- Team Table -->
  <div class="overflow-x-auto rounded-xl shadow-xl bg-white/70 backdrop-blur-md border border-gray-200" id="teamsTable">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-800 text-white">
        <tr>
          <th class="py-3 px-4 text-left">Leader Name</th>
          <th class="py-3 px-4 text-left">Phone</th>
          <th class="py-3 px-4 text-left">Email</th>
          <th class="py-3 px-4 text-left">Project Repo</th>
          <th class="py-3 px-4 text-left">Status</th>
          <th class="py-3 px-4 text-left">Update</th>
        </tr>
      </thead>
      <tbody id="tableBody">
        <!-- Dynamic rows -->
      </tbody>
    </table>
  </div>

  <!-- Pagination Controls -->
  <div class="flex justify-center mt-6 space-x-2" id="paginationControls"></div>

  <script>
    const data = <?= json_encode($teams) ?>;
    const rowsPerPage = 5;
    let currentPage = 1;

    function renderTable(page) {
      const start = (page - 1) * rowsPerPage;
      const end = start + rowsPerPage;
      const sliced = data.slice(start, end);
      const tbody = document.getElementById("tableBody");
      tbody.innerHTML = '';

      sliced.forEach(row => {
        tbody.innerHTML += `
        <tr class="border-b border-gray-300 hover:bg-gray-50">
          <td class="py-3 px-4 font-medium">${row.leaderName}</td>
          <td class="py-3 px-4">${row.leaderPhone}</td>
          <td class="py-3 px-4">${row.leaderEmail}</td>
          <td class="py-3 px-4">
            <a href="${row.projectRepo}" target="_blank" class="text-blue-600 flex items-center gap-1 hover:underline">
              <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 0C5.37 0...z" />
              </svg> View
            </a>
          </td>
          <td class="py-3 px-4">
            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full ${
              row.status === 'Accepted' ? 'bg-green-200 text-green-800' :
              row.status === 'Declined' ? 'bg-red-200 text-red-800' :
              row.status === 'Selected' ? 'bg-indigo-200 text-indigo-800' :
              'bg-gray-200 text-gray-700'
            }">${row.status}</span>
          </td>
          <td class="py-3 px-4">
            <form method="POST" class="flex gap-2 items-center">
              <input type="hidden" name="team_id" value="${row.id}">
              <select name="status" class="px-2 py-1 rounded border">
                <option value="Submitted"${row.status === 'Submitted' ? ' selected' : ''}>Submitted</option>
                <option value="Accepted"${row.status === 'Accepted' ? ' selected' : ''}>Accepted</option>
                <option value="Declined"${row.status === 'Declined' ? ' selected' : ''}>Declined</option>
                <option value="Selected"${row.status === 'Selected' ? ' selected' : ''}>Selected</option>
              </select>
              <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Update</button>
            </form>
          </td>
        </tr>`;
      });

      renderPagination();
    }

    function renderPagination() {
      const totalPages = Math.ceil(data.length / rowsPerPage);
      const controls = document.getElementById("paginationControls");
      controls.innerHTML = '';
      for (let i = 1; i <= totalPages; i++) {
        controls.innerHTML += `<button onclick="goToPage(${i})" class="px-3 py-1 rounded ${i === currentPage ? 'bg-black text-white' : 'bg-gray-200 hover:bg-gray-300'}">${i}</button>`;
      }
    }

    function goToPage(page) {
      currentPage = page;
      renderTable(page);
    }

    // Initial render
    renderTable(currentPage);
  </script>

</body>
</html>
