document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("workoutForm");
    const logsContainer = document.getElementById("logsContainer");
    const searchResultsContainer = document.getElementById("searchResults");
    const adviceForm = document.getElementById("adviceForm");
    const adviceBox = document.getElementById("adviceBox");
    const searchForm = document.getElementById("searchForm");
    const welcomeText = document.getElementById("welcomeUser");

    // Format date to MM/DD/YY
    function formatDate(dateStr) {
        const date = new Date(dateStr);
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');
        const yy = String(date.getFullYear()).slice(-2);
        return `${mm}/${dd}/${yy}`;
    }

    // Load username
    if (welcomeText) {
        fetch("/userinfo")
            .then(res => res.json())
            .then(data => {
                if (data.username) {
                    welcomeText.textContent = `Welcome, ${data.username}!`;
                }
            });
    }

    // Display logs
    function displayLogs(logs, container) {
        if (!Array.isArray(logs) || logs.length === 0) {
            container.innerHTML = "<p>No logs found.</p>";
            return;
        }

        container.innerHTML = logs.map(log => `
            <div class="log-entry">
                <p><strong>Date:</strong> ${formatDate(log.date)}</p>
                <p><strong>Exercise:</strong> ${log.exercise}</p>
                <p><strong>Weight:</strong> ${log.weight || 'N/A'}</p>
                <p><strong>Sets:</strong> ${log.sets || 'N/A'}</p>
                <p><strong>Reps:</strong> ${log.reps || 'N/A'}</p>
                <p><strong>Time:</strong> ${log.time || 'N/A'}</p>
                <p><strong>Distance:</strong> ${log.distance || 'N/A'}</p>
                <p><strong>Calories:</strong> ${log.calories || 'N/A'}</p>
                <button class="edit-btn" data-id="${log.id}">Edit</button>
                <button class="delete-btn" data-id="${log.id}">Delete</button>
            </div>
        `).join("");

        // Edit handler
        document.querySelectorAll(".edit-btn").forEach(btn => {
            btn.addEventListener("click", () => {
                const id = btn.dataset.id;
                const entry = logs.find(l => l.id == id);
                if (entry) {
                    form.setAttribute("data-edit-id", id);
                    form.date.value = entry.date;
                    form.exercise.value = entry.exercise;
                    form.weight.value = entry.weight;
                    form.sets.value = entry.sets;
                    form.reps.value = entry.reps;
                    form.time.value = entry.time || '';
                    form.distance.value = entry.distance || '';
                    form.calories.value = entry.calories || '';
                }
            });
        });

        // Delete handler
        document.querySelectorAll(".delete-btn").forEach(btn => {
            btn.addEventListener("click", () => {
                const id = btn.dataset.id;
                if (confirm("Are you sure you want to delete this log?")) {
                    fetch("/logs/delete", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: `id=${id}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) loadLogs(); // Refresh view
                        else alert("Failed to delete log.");
                    });
                }
            });
        });
    }

    // Load all logs
    function loadLogs() {
        fetch("/logs")
            .then(res => res.json())
            .then(logs => {
                displayLogs(logs, logsContainer); // Display the main logs
            });
    }

    // Submit new or updated workout
    if (form) {
        form.addEventListener("submit", function (e) {
            e.preventDefault();
            const data = new URLSearchParams();

            data.append("date", form.date.value);
            data.append("exercise", form.exercise.value);
            data.append("weight", form.weight.value);
            data.append("sets", form.sets.value || "N/A");
            data.append("reps", form.reps.value || "N/A");
            data.append("time", form.time.value);
            data.append("distance", form.distance.value);
            data.append("calories", form.calories.value);

            const id = form.getAttribute("data-edit-id");
            const endpoint = id ? "/logs/update" : "/logs";
            if (id) data.append("id", id);

            fetch(endpoint, {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: data.toString()
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    form.reset();
                    form.removeAttribute("data-edit-id");
                    loadLogs(); // Refresh view
                } else {
                    alert(result.error || "Failed to save workout.");
                }
            });
        });
    }

    // Submit advice
    if (adviceForm) {
        adviceForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const question = document.getElementById("adviceQuestion").value;
            adviceBox.textContent = "Thinking...";
            fetch("/advice?question=" + encodeURIComponent(question))
                .then(res => res.json())
                .then(data => {
                    adviceBox.textContent = data.advice || "No advice found.";
                })
                .catch(() => {
                    adviceBox.textContent = "Something went wrong.";
                });
        });
    }

    // Search logs by exercise
    if (searchForm) {
        searchForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const keyword = document.getElementById("searchExercise").value.trim().toLowerCase();
            if (keyword === '') return loadLogs(); // Clear search

            fetch("/logs?exercise=" + encodeURIComponent(keyword))
                .then(res => res.json())
                .then(logs => {
                    displayLogs(logs, searchResultsContainer); // Show search results below logs
                })
                .catch(() => {
                    searchResultsContainer.innerHTML = "<p>Could not fetch search results.</p>";
                });
        });
    }

    // Initial load
    loadLogs();
});
