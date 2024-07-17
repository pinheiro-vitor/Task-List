<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Task List</h2>
        </template>

        <!-- Adicionar botão "Adicionar Task" -->
        <div class="mt-4 min-w-full flex items-center justify-center bg-teal-lightest font-sans">
            <div class="bg-white rounded shadow p-6 m-4 w-full">
                <div v-if="true" class="text-blue-600 mb-4">
                    {{ $page.props.flash.message }}
                </div>
                <div class="mb-4 flex justify-end">
                    <button class="px-2 py-1 bg-blue-500 text-white rounded font-bold uppercase" @click="openModal">Adicionar task</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Título
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Descrição
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-20 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Data
                                </th>
                                <th class="px-20 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="task in tasks" :key="task.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ task.id }}
                                </td>
                                <td class="px-8 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ task.title }}
                                </td>
                                <td class="px-10 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ task.description }}
                                </td>
                                <td class="px-10 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <input type="checkbox" v-model="task.completed" @change="updateTaskStatus(task)">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ task.created_at }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <button @click="openModal" class="px-2 mr-2 py-1 bg-green-600 text-white rounded font-bold uppercase">
                                        Editar
                                    </button>
                                    <button @click="destroy(task.id)" type="button" class="px-2 py-1 bg-red-600 text-white rounded font-bold uppercase">
                                        Apagar
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Modal de Adição de Task -->
        <div v-if="showModal" class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mt-3 text-center">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Nova Task
                            </h3>
                            <div class="mt-2">
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker" placeholder="Title" v-model="form.title">
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker mt-4" placeholder="Description" v-model="form.description">
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
                        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-500 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="submit">
                            Salvar
                        </button>
                        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="closeModal">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>



<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Head } from '@inertiajs/inertia-vue3';

export default {
    components: {
        AuthenticatedLayout,
        Head,
    },
    props: {
        tasks: Array
    },
    setup(props) {
        const destroy = (id) => {
            if (confirm('Você tem certeza que deseja apagar esta task?')) {
                Inertia.delete(`/tasks/${id}`);
            }
        };
        
        const form = reactive({
            title: '',
            description: ''
        });

        const tasks = ref(props.tasks);
        const showModal = ref(false);

        function openModal() {
            showModal.value = true;
        }

        function closeModal() {
            showModal.value = false;
        }

        function submit() {
            if (form.title !== '' && form.description !== '') {
                Inertia.post('/tasks', form, {
                    onSuccess: () => {
                        tasks.value.push({
                            id: tasks.value.length + 1,
                            title: form.title,
                            description: form.description,
                            completed: false,
                            created_at: new Date().toISOString().split('T')[0]
                        });
                        form.title = '';
                        form.description = '';
                        closeModal();
                    }
                });
            }
        }

        function updateTaskStatus(task) {
        const originalCompleted = task.completed;
        Inertia.put(`/tasks/${task.id}`, { completed: task.completed }, {
            preserveState: true,
            onSuccess: () => {
                // A atualização do estado local é feita automaticamente, mas você pode implementar lógicas adicionais aqui se necessário.
            },
            onError: () => {
                // Em caso de erro, reverta a mudança no estado local para refletir o estado atual no servidor
                const index = tasks.value.findIndex(t => t.id === task.id);
                if (index !== -1) {
                    tasks.value[index].completed = originalCompleted;
                }
            }
        });
        }

        return {
            form,
            tasks,
            showModal,
            openModal,
            closeModal,
            submit,
            updateTaskStatus,
            destroy
        };
    }
};
</script>
