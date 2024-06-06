document.addEventListener("DOMContentLoaded", function () {
  const apiUrl = "http://localhost/IoT_Project_RFID/api";
  const dataTable = document.querySelector("#data-table tbody");
  const refreshDataButton = document.getElementById("refresh-data");
  const deleteAllButton = document.getElementById("delete-all");
  const getLastDataButton = document.getElementById("get-last-data");
  const addForm = document.getElementById("add-form");
  const lastDataDiv = document.getElementById("last-data");

  // Function to fetch all data and display in the table
  function fetchData() {
    fetch(`${apiUrl}/getAll.php`)
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }
        return response.json();
      })
      .then((data) => {
        if (data.status !== "200" || !Array.isArray(data.data)) {
          throw new Error("Data received is not an array or status is not 200");
        }
        dataTable.innerHTML = "";
        data.data.forEach((item) => {
          const row = document.createElement("tr");
          row.innerHTML = `
                    <td data-id="${item.id}">${item.id}</td>
                    <td>${item.nama}</td>
                    <td>${item.tag}</td>
                    <td>${item.createdAt}</td>
                    <td>${item.updatedAt}</td>
                    <td>
                    <div class="btn-delete">
                        <button onclick="deleteData(${item.id})"><i class='bx bx-trash'></i></button>
                    </div>
                    <div class="btn-edit">
                        <button onclick="editData(${item.id}, '${item.tag}')"><i class='bx bx-edit' ></i></button>
                    </div>
                    </td>
                `;
          dataTable.appendChild(row);
        });
      })
      .catch((error) => {
        console.error("Error fetching data:", error);
        alert("Failed to fetch data: " + error.message);
      });
  }

  // Function to add new data
  addForm.addEventListener("submit", function (event) {
    event.preventDefault();
    const newName = document.getElementById("new-name").value;
    const newTag = document.getElementById("new-tag").value;

    fetch(`${apiUrl}/create_data.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ nama: newName, tag: newTag }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status !== "201") {
          throw new Error("Error adding new data");
        }
        fetchData();
        addForm.reset(); // Reset the form after successful addition
      })
      .catch((error) => {
        console.error("Error adding new data:", error);
      });
  });

  // Function to delete data by ID
  window.deleteData = function (id) {
    fetch(`${apiUrl}/delete_data.php?id=${id}`, {
      method: "DELETE",
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }
        return response.json();
      })
      .then((data) => {
        if (data.status === "200") {
          // Data deleted successfully
          fetchData(); // Refresh data after successful deletion
        } else {
          // Show error message
          throw new Error("Error deleting data: " + data.message);
        }
      })
      .catch((error) => {
        console.error("Error deleting data:", error.message);
        // Show an alert or other user-friendly message
        alert("Failed to delete data: " + error.message);
      });
  };

  // Function to edit data
  window.editData = function (id, oldTag, oldName) {
    const newName = prompt("Masukkan Nama Baru: ", oldName);
    const newTag = prompt("Masukkan No. RFID Baru: ", oldTag);

    if ((newTag && newTag !== oldTag) || (newName && newName !== oldName)) {
      fetch(`${apiUrl}/update_data.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id: id, nama: newName, tag: newTag }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status !== "200") {
            throw new Error("Error updating data");
          }
          fetchData();
        })
        .catch((error) => {
          console.error("Error updating data:", error);
        });
    }
  };

  // Function to delete all data
  deleteAllButton.addEventListener("click", function () {
    if (confirm("Apakah Anda yakin ingin menghapus semua data?")) {
      fetch(`${apiUrl}/deleteAll.php?delete_all`, {
        method: "DELETE",
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.json();
        })
        .then((data) => {
          if (data.status === "200") {
            // All data deleted successfully
            fetchData(); // Refresh data after successful deletion
          } else if (data.status === "404") {
            // No data found to delete
            console.warn(data.message); // Log the message to console
            fetchData(); // Refresh data to reflect current state
          } else {
            // Other error
            throw new Error("Error deleting all data: " + data.message);
          }
        })
        .catch((error) => {
          console.error("Error deleting all data:", error.message);
          // Show an alert or other user-friendly message
          alert("Failed to delete all data: " + error.message);
        });
    }
  });

  // Function to get the last data
  getLastDataButton.addEventListener("click", function () {
    fetch(`${apiUrl}/getLast.php`)
      .then((response) => response.json())
      .then((data) => {
        if (data.status !== "200") {
          throw new Error("Error fetching last data");
        }
        const lastData = data.data;
        lastDataDiv.innerHTML = `
            <p>ID: ${lastData.id}</p>
            <p>Nama: ${lastData.nama}</p>
            <p>No. RFID: ${lastData.tag}</p>
            <p>Waktu Presensi: ${lastData.createdAt}</p>
            <p>Waktu Diedit: ${lastData.updatedAt}</p>
          `;
      })
      .catch((error) => {
        console.error("Error fetching last data:", error);
      });
  });

  // Refresh data when button is clicked
  refreshDataButton.addEventListener("click", fetchData);

  // Initial data fetch
  fetchData();
});
