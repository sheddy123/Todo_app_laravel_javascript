<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Todo coursework</title>
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet" />
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,400;0,700;0,900;1,400&family=Montserrat:ital,wght@0,400;0,700;0,900;1,400&display=swap"
        rel="stylesheet">

    <!-- Other head elements -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Styles -->
    <style>
    </style>
</head>

<body>
    <!-- App Wrapper -->
    <main class="app">
        <!-- Greeting -->
        <section class="greeting">
            <h2 class="title">
                Hi, <input type="text" id="name" placeholder="Insert your name here" /> 
            </h2>
        </section>
        <!-- End of Greeting -->

        <!-- New Todo -->
        <section class="create-todo">
            <form id="new-todo-form">
                <div class="options">
                    <div>
                        <h4>Add a group item</h4>
                        <input type="text" placeholder="Add group item" name="group" id="group" />
                        {{-- <input type="submit" value="Add group" /> --}}
                    </div>
                    <div></div>

                    <div>
                        <h4>What's on your todo?</h4>
                        <input type="text" placeholder="Add todo item" name="content" id="content" />
                        <input type="submit" value="Add todo" />
                    </div>

                </div>


            </form>
        </section>
        <!-- End of New Todo -->

        <!-- Todo List -->
        <section class="todo-list">
            <h3>TODO LIST</h3>
            <div class="list" id="todo-list"></div>
        </section>
        <!-- End of Todo List -->

    </main>
    <!-- End of App Wrapper -->
    <script type="text/javascript">
    var todos = [];
        window.addEventListener('load', () => {
            const nameInput = document.querySelector('#name');
            const newTodoForm = document.querySelector('#new-todo-form');

            const username = localStorage.getItem('username') || '';

            nameInput.value = username;

            nameInput.addEventListener('change', (e) => {
                localStorage.setItem('username', e.target.value);
            });


            newTodoForm.addEventListener('submit', e => {
                e.preventDefault();

                const todo = {
                    content: e.target.elements.content.value,
                    category: e.target.elements.category?.value,
                    group: e.target.elements.group.value,
                    done: false,
                    createdAt: new Date().getTime()
                };
                if (document.getElementById('group').value !== "") {   
                    todo.category = document.getElementById('group').value;
                }
                // Send the new todo data to the server using AJAX
                fetch('/api/todos', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(todo)
                    })
                    .then(response => response.json())
                    .then(todoFromServer => {
                        // Update the todos array with the newly created todo from the server
                        todos.push(todoFromServer);

                        // Reset the form
                        e.target.reset();

                        // Update the display with the new todo
                        DisplayTodos();
                    });
            });

            DisplayTodos()
        })

        function getDistinctCategories(todos) {
            const categoriesSet = new Set();
            todos.forEach(todo => {
                categoriesSet.add(todo.category);
            });
            return Array.from(categoriesSet);
        }
        

        function DisplayTodos() {
            // Fetch the todos from the server using AJAX
            fetch('/api/todos')
                .then(response => response.json())
                .then(data => {
                    const todoList = document.querySelector('#todo-list');
                    todoList.innerHTML = "";

                    // Call the function to get the distinct categories
                    const distinctCategories = getDistinctCategories(data);
                    console.log(distinctCategories);

                    // Get the first div inside <div class="options">
                    // Get all div elements inside <div class="options">
                    const divElements = document.querySelectorAll('.options div');

                    // Check if there is a second div (index 1) in the NodeList
                    if (divElements.length >= 2) {
                        const secondDiv = divElements[1];
// Clear the existing <label> tags after the secondDiv
    let nextSibling = secondDiv.nextElementSibling;
  while (nextSibling) {
    if (nextSibling.tagName === 'LABEL') {
      const toRemove = nextSibling;
      nextSibling = nextSibling.nextElementSibling;
      toRemove.remove();
    } else {
      break; // Stop removing elements when a non-label element is encountered
    }
  }
                        // Create and insert the text "Pick a group" at the top of the second div inside <div class="options">
                        //     const pickGroupText = document.createElement('h4');
                        //   pickGroupText.textContent = 'Pick a group';
                        //   secondDiv.insertAdjacentElement('afterbegin', pickGroupText);

                        // Populate the distinct categories just below the second div inside <div class="options">
                        distinctCategories.forEach(category => {
                            const label = document.createElement('label');
                            const input = document.createElement('input');
                            const span = document.createElement('span');
                            const div = document.createElement('div');

                            input.type = 'radio';
                            input.name = 'category';
                            input.value = category;
                            span.classList.add('bubble');
                            span.classList.add(category.replace(/\s+/g, "").toLowerCase());
                            div.textContent = category;

                            label.appendChild(input);
                            label.appendChild(span);
                            label.appendChild(div);
                            secondDiv.insertAdjacentElement('afterend', label);
                        });
                    }

                    data.forEach(todo => {
                        const todoItem = document.createElement('div');
                        todoItem.classList.add('todo-item');

                        const label = document.createElement('label');
                        const input = document.createElement('input');
                        const span = document.createElement('span');
                        const content = document.createElement('div');
                        const actions = document.createElement('div');
                        const edit = document.createElement('button');
                        const deleteButton = document.createElement('button');

                        input.type = 'checkbox';
                        input.checked = todo.done;
                        span.classList.add('bubble');
                        if (todo.category == 'personal') {
                            span.classList.add('personal');
                        } else {
                            span.classList.add('business');
                        }
                        content.classList.add('todo-content');
                        actions.classList.add('actions');
                        edit.classList.add('edit');
                        deleteButton.classList.add('delete');

                        content.innerHTML = `<span><b><i style="font-size:11px">Group: ${todo.category}</i></b></span><input type="text" value="    ${todo.content}" readonly>`;
                        edit.innerHTML = 'Edit';
                        deleteButton.innerHTML = 'Delete';

                        label.appendChild(input);
                        label.appendChild(span);
                        actions.appendChild(edit);
                        actions.appendChild(deleteButton);
                        todoItem.appendChild(label);
                        todoItem.appendChild(content);
                        todoItem.appendChild(actions);

                        todoList.appendChild(todoItem);

                        if (todo.done) {
                            todoItem.classList.add('done');
                        }

                        input.addEventListener('change', (e) => {
                            todo.done = e.target.checked;
                            updateTodoOnServer(todo);
                        });

                        edit.addEventListener('click', (e) => {
                            const input = content.querySelector('input');
                            input.removeAttribute('readonly');
                            input.focus();
                        });

                        content.querySelector('input').addEventListener('blur', (e) => {
                            todo.content = e.target.value;
                            updateTodoOnServer(todo);
                        });

                        deleteButton.addEventListener('click', (e) => {
                            deleteTodoOnServer(todo);
                        });
                    });
                });
        }

        function updateTodoOnServer(todo) {
            // Send the updated todo data to the server using AJAX
            fetch(`/api/todos/${todo.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(todo)
                })
                .then(response => response.json())
                .then(updatedTodo => {
                    // Update the corresponding todo in the local todos array
                    const index = todos.findIndex(t => t.id === updatedTodo.id);
                    if (index !== -1) {
                        todos[index] = updatedTodo;
                    }
                    // Update the display with the updated todo
                    DisplayTodos();
                });
        }

        function deleteTodoOnServer(todo) {
            // Send a DELETE request to the server to delete the todo
            fetch(`/api/todos/${todo.id}`, {
                    method: 'DELETE'
                })
                .then(response => {
                    // console.log("Todos is" + todos);
                    // // Remove the deleted todo from the local todos array
                    // todos = todos.filter(t => t.id !== todo.id);
                    // Update the display after deletion
                   DisplayTodos();
                });
        }
    </script>
</body>

</html>
