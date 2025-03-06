document.addEventListener('DOMContentLoaded', function(){

    const tasks = [
        {
            id: 1,
            title: "Complete project report",
            description: "Prepare and submit the project report",
            dueDate: "2024-12-01",
            comments: []
        },
        {
            id:2,
            title: "Team Meeting",
            description: "Get ready for the season",
            dueDate: "2024-12-01",
            comments: []
        },
        {
            id: 3,
            title: "Code Review",
            description: "Check partners code",
            dueDate: "2024-12-01",
            comments: []
        }
    ];
    
    function loadTasks(){
        const taskList = document.getElementById('task-list');
        taskList.innerHTML = '';
        tasks.forEach(function(task){
            const taskCard = document.createElement('div');
            taskCard.className = 'col-md-4 mb-3';
            taskCard.innerHTML = `
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">${task.title}</h5>
                    <p class="card-text">${task.description}</p>
                    <p class="card-text"><small class="text-muted">Due: ${task.dueDate}</small></p>
                    <input type="text" class="form-control mb-2" id="comment-input-${task.id}" placeholder="Escribe un comentario">
                    <button class="btn btn-primary btn-sm add-comment-btn" data-task-id="${task.id}">Agregar comentario</button>
                    <ul class="list-group mt-2" id="comment-list-${task.id}"></ul>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <button class="btn btn-secondary btn-sm edit-task" data-id="${task.id}">Edit</button>
                    <button class="btn btn-danger btn-sm delete-task" data-id="${task.id}">Delete</button>
                </div>
            </div>`;
            taskList.appendChild(taskCard);
            renderComments(task.id);
        });

        document.querySelectorAll(".add-comment-btn").forEach(button => {
            button.addEventListener("click", function() {
                const taskId = parseInt(this.getAttribute("data-task-id"));
                addComment(taskId);
            });
        });

        document.querySelectorAll('.edit-task').forEach(function(button){
            button.addEventListener('click', handleEditTask);
        });

        document.querySelectorAll('.delete-task').forEach(function(button){
            button.addEventListener('click', handleDeleteTask);
        });
    }
    
    function addComment(taskId){
        const inputField = document.getElementById(`comment-input-${taskId}`);
        const commentText = inputField.value.trim();
        if(commentText !== ""){
            const task = tasks.find(t => t.id === taskId);
            task.comments.push(commentText);
            inputField.value = "";
            renderComments(taskId);
        }
    }
    
    function renderComments(taskId){
        const task = tasks.find(t => t.id === taskId);
        const commentList = document.getElementById(`comment-list-${taskId}`);
        commentList.innerHTML = "";
        task.comments.forEach((comment, index) => {
            const listItem = document.createElement("li");
            listItem.className = "list-group-item d-flex justify-content-between align-items-center";
            listItem.innerHTML = `
                ${comment} 
                <button class="btn btn-danger btn-sm delete-comment-btn" data-task-id="${taskId}" data-comment-index="${index}">X</button>
            `;
            commentList.appendChild(listItem);
        });

        document.querySelectorAll(".delete-comment-btn").forEach(button => {
            button.addEventListener("click", function() {
                const taskId = parseInt(this.getAttribute("data-task-id"));
                const commentIndex = parseInt(this.getAttribute("data-comment-index"));
                deleteComment(taskId, commentIndex);
            });
        });
    }
    
    function deleteComment(taskId, commentIndex){
        const task = tasks.find(t => t.id === taskId);
        task.comments.splice(commentIndex, 1);
        renderComments(taskId);
    }

    function handleEditTask(event){
        const taskId = parseInt(event.target.dataset.id);
        const task = tasks.find(t => t.id === taskId);

        if (task) {
            document.getElementById('task-id').value = task.id;
            document.getElementById('task-title').value = task.title;
            document.getElementById('task-desc').value = task.description;
            document.getElementById('due-date').value = task.dueDate;

            const modal = new bootstrap.Modal(document.getElementById('taskModal'));
            modal.show();
        }
    }

    function handleDeleteTask(event){
        const taskId = parseInt(event.target.dataset.id);
        const taskIndex = tasks.findIndex(t => t.id === taskId);

        if (taskIndex !== -1) {
            tasks.splice(taskIndex, 1);
            loadTasks(); 
        }
    }

    document.getElementById('task-form').addEventListener('submit', function(e){
        e.preventDefault();

        let currentTaskId = document.getElementById('task-id').value;
        const taskTitle = document.getElementById('task-title').value;
        const taskDesc = document.getElementById('task-desc').value;
        const dueDate = document.getElementById('due-date').value;

        if (currentTaskId) {
            const taskIndex = tasks.findIndex(t => t.id === parseInt(currentTaskId));
            tasks[taskIndex] = {
                id: parseInt(currentTaskId),
                title: taskTitle,
                description: taskDesc,
                dueDate: dueDate,
                comments: tasks[taskIndex].comments 
            };
        } else {
            const newTask = {
                id: tasks.length > 0 ? Math.max(...tasks.map(t => t.id)) + 1 : 1,
                title: taskTitle,
                description: taskDesc,
                dueDate: dueDate,
                comments: []
            };
            tasks.push(newTask);
        }

        document.getElementById('task-id').value = '';
        e.target.reset();

        loadTasks();

        const modal = bootstrap.Modal.getInstance(document.getElementById('taskModal'));
        modal.hide();
    });

    loadTasks();
});