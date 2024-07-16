<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

// Estado para o formulário e as tasks
const form = ref({
    content: ''
});

const tasks = ref([
    // Exemplo de tarefas
    { id: 1, description: 'Tarefa 1', completed: false },
    { id: 2, description: 'Tarefa 2', completed: true }
]);

// Função para adicionar uma nova task
const submit = () => {
    if (form.value.content.trim() !== '') {
        tasks.value.push({
            id: tasks.value.length + 1,
            description: form.value.content,
            completed: false
        });
        form.value.content = '';
    }
};

// Função para atualizar o status da task
const updateStatus = (task) => {
    task.completed = !task.completed;
};

// Função para deletar uma task
const deleteTask = (task) => {
    tasks.value = tasks.value.filter(t => t.id !== task.id);
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Task List</h2>
        </template>

        <!-- component -->
        <div class="h-100 w-full flex items-center justify-center bg-teal-lightest font-sans">
            <div class="bg-white rounded shadow p-6 m-4 w-full lg:w-3/4 lg:max-w-lg">
                <div class="mb-4">
                    <h1 class="text-grey-darkest">Task List</h1>
                    <div class="flex mt-4">
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 mr-4 text-grey-darker" placeholder="Add Task" v-model="form.content">
                        <button class="flex-no-shrink p-2 border-2 rounded-lg text-teal border-teal text-white bg-blue-500 hover:bg-blue-700" @click="submit">Add</button>
                    </div>
                </div>
                <div>
                    <div class="flex mb-4 items-center" v-for="task in tasks" :key="task.id">
                        <p class="w-full text-grey-darkest">{{ task.description }}</p>
                        <button v-if="task.completed" class="flex-no-shrink w-1/3 p-2 ml-4 mr-2 border-2 rounded-lg border-grey" @click="updateStatus(task)">Not Done</button>
                        <button v-else class="flex-no-shrink p-2 ml-4 mr-2 border-2 rounded-lg border-green text-white bg-green-500 hover:bg-green-700" @click="updateStatus(task)">Done</button>
                        <button class="flex-no-shrink p-2 ml-2 border-2 rounded-lg text-red border-red text-white bg-red-500 hover:bg-red-700" @click="deleteTask(task)">Remove</button>
                    </div>
                </div>
            </div>
        </div> 
    </AuthenticatedLayout>
</template>
