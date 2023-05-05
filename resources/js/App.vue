<script>
const STORAGE_KEY = 'vue-todomvc'

const filters = {
    all: (todos) => todos,
    active: (todos) => todos.filter((todo) => !todo.completed),
    completed: (todos) => todos.filter((todo) => todo.completed)
}

export default {
    // app initial state
    data: () =>
    ({
        // todos: JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'),
        todos: [],


        editedTodo: null,
        visibility: 'all'
    }),

    // watch todos change for localStorage persistence
    watch: {
        todos: {
            handler(todos) {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(todos))
            },
            deep: true
        }
    },

    mounted() {
        window.addEventListener('hashchange', this.onHashChange);
        this.onHashChange();

        // console.log(this.todos);

        axios.post('index').then(({data})=>{
            console.log("item index: ",data);

            this.todos = data;

        }).catch(({response})=>{

        }).finally(()=>{

        })

    },

    computed: {
        filteredTodos() {
            return filters[this.visibility](this.todos)
        },
        remaining() {
            return filters.active(this.todos).length
        }
    },

    // methods that implement data logic.
    // note there's no DOM manipulation here at all.
    methods: {


        toggleAll(e) {
            this.todos.forEach((todo) => (todo.completed = e.target.checked))

        },

        async addTodo(e) {
            const value = e.target.value.trim()
            if (!value) {
                return
            }

            console.log(JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'));

            e.target.value = ''
            await axios.post('add-item',{'value':value}).then(({data})=>{
                console.log("item added",data);

                this.todos.push({
                    id: data.index,
                    title: data.value,
                    completed: false
                })

            }).catch(({response})=>{

            }).finally(()=>{

            })
        },

        async removeTodo(todo) {

            console.log(todo.id);

            await axios.post('destroy-item',{'id':[todo.id]}).then(({data})=>{
                console.log("item deleted",data);

                this.todos.splice(this.todos.indexOf(todo), 1);

            }).catch(({response})=>{

            }).finally(()=>{

            })
        },

        editTodo(todo) {
            this.beforeEditCache = todo.title
            this.editedTodo = todo
        },

        doneEdit(todo) {
            if (!this.editedTodo) {
                return
            }
            this.editedTodo = null
            let newTodo = todo.title.trim()

            axios.post('update-item',{'id':todo.id, 'title': newTodo}).then(({data})=>{
                console.log("item updated",data);

                todo.title = newTodo;

                if (!todo.title) {
                    this.removeTodo(todo)
                }

            }).catch(({response})=>{

            }).finally(()=>{

            })
        },

        cancelEdit(todo) {
            this.editedTodo = null
            todo.title = this.beforeEditCache
        },

        async removeCompleted() {

            let allCompleted = filters.completed(this.todos);

            await axios.post('destroy-item',{'id':allCompleted}).then(({data})=>{
                console.log("items deleted",data);

                // this.todos.splice(this.todos.indexOf(todo), 1);

                this.todos = filters.active(this.todos);

            }).catch(({response})=>{

            }).finally(()=>{

            })

            // console.log(allCompleted);


        },

        onHashChange() {
            var visibility = window.location.hash.replace(/#\/?/, '')
            if (filters[visibility]) {
                this.visibility = visibility
            } else {
                window.location.hash = ''
                this.visibility = 'all'
            }
        },

        completedTodo(todo) {
            console.log('hejaja ', todo);

            axios.post('completed-item',{'id':todo.id, 'completed': !todo.completed}).then(({data})=>{
                console.log("item completed",data);

            }).catch(({response})=>{

            }).finally(()=>{

            })

        }
    }
}
</script>

<template>
    <section class="todoapp">
        <header class="header">
            <h1>Nakup</h1>
            <input
                class="new-todo"
                autofocus
                placeholder="Kaj je potrebno kupiti?"
                @keyup.enter="addTodo"
            >
        </header>
        <section class="main" v-show="todos.length">
            <input
                id="toggle-all"
                class="toggle-all"
                type="checkbox"
                :checked="remaining === 0"
                @change="toggleAll"
            >
            <label for="toggle-all">Mark all as complete</label>
            <ul class="todo-list">
                <li
                    v-for="todo in filteredTodos"
                    class="todo"
                    :key="todo.id"
                    :class="{ completed: todo.completed, editing: todo === editedTodo }"
                >
                    <div class="view">
                        <input class="toggle" type="checkbox" v-model="todo.completed" @click="completedTodo(todo)">
                        <label @dblclick="editTodo(todo)">{{ todo.title }}</label>
                        <button class="destroy" @click="removeTodo(todo)"></button>
                    </div>
                    <input
                        v-if="todo === editedTodo"
                        class="edit"
                        type="text"
                        v-model="todo.title"
                        @vnode-mounted="({ el }) => el.focus()"
                        @blur="doneEdit(todo)"
                        @keyup.enter="doneEdit(todo)"
                        @keyup.escape="cancelEdit(todo)"
                    >
                </li>
            </ul>
        </section>
        <footer class="footer" v-show="todos.length">
      <span class="todo-count">
        <strong>{{ remaining }}</strong>
        <span>{{ remaining === 1 ? ' item' : ' izdelki' }} ostajajo</span>
      </span>
            <ul class="filters">
                <li>
                    <a href="#/all" :class="{ selected: visibility === 'all' }">Vsi</a>
                </li>
                <li>
                    <a href="#/active" :class="{ selected: visibility === 'active' }">Kupiti</a>
                </li>
                <li>
                    <a href="#/completed" :class="{ selected: visibility === 'completed' }">Že kupljeno</a>
                </li>
            </ul>
            <button class="clear-completed" @click="removeCompleted" v-show="todos.length > remaining">
                Odstrani že kupljeno
            </button>
        </footer>
    </section>
</template>

<style>
    /*@import "https://unpkg.com/todomvc-app-css@2.4.1/index.css";*/
</style>
