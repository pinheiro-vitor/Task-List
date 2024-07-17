<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="mb-2 flex flex-row justify-between align-middle">
                <div class="flex align-middle justify-center h-full p-2"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Task List</h2></div>
                <button class="px-2 py-1 bg-blue-500 text-white rounded font-bold uppercase" @click="openModal">Adicionar task</button>
            </div>
        </template>

        <div class="mt-4 min-w-full flex items-center justify-center bg-teal-lightest font-sans">
            <div class="bg-white rounded shadow p-6 m-4 w-full">
                <transition name="fade">
                    <div v-if="flashMessage" class="center absolute bg-blue-100 text-blue-600 p-1 rounded shadow-lg">
                        {{ flashMessage }}
                    </div>
                </transition>
                <div class="mb-4 flex justify-end">
                    Pesquisa aqui.
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tempo restante</th>
                                <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="task in paginatedTasks" :key="task.id">
                                <td class="px-6 py-4 whitespace-nowrap text-center text-gray-900">{{ task.id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-gray-900">{{ task.title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-gray-900">
                                    <input type="checkbox" v-model="task.completed" @change="updateTaskStatus(task)">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-gray-900">{{ calculateTimeRemaining(task.due_date, task.due_time) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-gray-900">
                                    <button @click="openDetailsModal(task)" class="px-2 mr-2 py-1 bg-yellow-500 text-white rounded font-bold uppercase">
                                        Visualizar
                                    </button>
                                    <button @click="openEditModal(task)" class="px-2 mr-2 py-1 bg-green-600 text-white rounded font-bold uppercase">
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
                <!-- Paginação -->
                <div class="mt-4 flex justify-between items-center">
                    <button class="px-4 py-2 bg-blue-500 text-white rounded font-bold" :disabled="currentPage === 1" @click="currentPage--">Anterior</button>
                    <div>Page {{ currentPage }} of {{ totalPages }}</div>
                    <button class="px-4 py-2 bg-blue-500 text-white rounded font-bold" :disabled="currentPage === totalPages" @click="currentPage++">Próxima</button>
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
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker mt-4" type="date" placeholder="Data de Vencimento" v-model="form.due_date">
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker mt-4" type="time" placeholder="Hora de Vencimento" v-model="form.due_time">
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
        <!-- Modal de Edição de Task -->
        <div v-if="showEditModal" class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mt-3 text-center">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Editar Task
                            </h3>
                            <div class="mt-2">
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker" placeholder="Title" v-model="editForm.title">
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker mt-4" placeholder="Description" v-model="editForm.description">
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker mt-4" type="date" placeholder="Data de Vencimento" v-model="editForm.due_date">
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker mt-4" type="time" placeholder="Hora de Vencimento" v-model="editForm.due_time">
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
                        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-500 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="updateTask">
                            Salvar
                        </button>
                        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="closeEditModal">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showDetailsModal" class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mt-3 text-center">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Detalhes da Task</h3>
                            <div class="mt-2">
                                <p><strong>Título:</strong> {{ detailsForm.title }}</p>
                                <p><strong>Descrição:</strong> {{ detailsForm.description }}</p>
                                <p><strong>Data Marcada:</strong> {{ detailsForm.due_date }}</p>
                                <p><strong>Hora Marcada:</strong> {{ detailsForm.due_time }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
                        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-500 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="closeDetailsModal">
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>



<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, reactive, onMounted, computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Head } from '@inertiajs/inertia-vue3';

export default {
    components: {
        AuthenticatedLayout,
        Head,
    },
    props: {
        tasks: Array,
        flash: Object
    },
    setup(props) {
        const currentPage = ref(1);
        const itemsPerPage = 5;

        const totalPages = computed(() => Math.ceil(tasks.value.length / itemsPerPage));

        const paginatedTasks = computed(() => {
            const startIndex = (currentPage.value - 1) * itemsPerPage;
            return tasks.value.slice(startIndex, startIndex + itemsPerPage);
        });

        const destroy = (id) => {
            if (confirm('Você tem certeza que deseja apagar esta task?')) {
                Inertia.delete(`/tasks/${id}`);
            }
        };

        const form = reactive({
            title: '',
            description: '',
            due_date: '',
            due_time: ''
        });

        const editForm = reactive({
            id: null,
            title: '',
            description: '',
            due_date: '',
            due_time: ''
        });

        const detailsForm = reactive({
            id: null,
            title: '',
            description: '',
            due_date: '',
            due_time: ''
        });

        const tasks = ref(props.tasks);
        const showModal = ref(false);
        const showEditModal = ref(false);
        const showDetailsModal = ref(false);
        const flashMessage = ref(props.flash.message);

        onMounted(() => {
            if (flashMessage.value) {
                setTimeout(() => {
                    flashMessage.value = '';
                }, 2250);
            }
        });

        function openModal() {
            showModal.value = true;
        }

        function closeModal() {
            showModal.value = false;
        }

        function openEditModal(task) {
            editForm.id = task.id;
            editForm.title = task.title;
            editForm.description = task.description;
            editForm.due_date = task.due_date;
            editForm.due_time = task.due_time;
            showEditModal.value = true;
        }

        function closeEditModal() {
            showEditModal.value = false;
            editForm.id = null;
            editForm.title = '';
            editForm.description = '';
            editForm.due_date = '';
            editForm.due_time = '';
        }

        function openDetailsModal(task) {
            detailsForm.id = task.id;
            detailsForm.title = task.title;
            detailsForm.description = task.description;
            detailsForm.due_date = task.due_date;
            detailsForm.due_time = task.due_time;
            showDetailsModal.value = true;
        }

        function closeDetailsModal() {
            showDetailsModal.value = false;
            detailsForm.id = null;
            detailsForm.title = '';
            detailsForm.description = '';
            detailsForm.due_date = '';
            detailsForm.due_time = '';
        }

        function submit() {
            if (form.title !== '' && form.description !== '') {
                const dueDateTime = `${form.due_date}T${form.due_time}`;
                Inertia.post('/tasks', { ...form, due_date_time: dueDateTime }, {
                    onSuccess: () => {
                        tasks.value.push({
                            id: tasks.value.length + 1,
                            title: form.title,
                            description: form.description,
                            due_date: form.due_date,
                            due_time: form.due_time,
                            completed: false,
                            created_at: new Date().toISOString().split('T')[0]
                        });
                        form.title = '';
                        form.description = '';
                        form.due_date = '';
                        form.due_time = '';
                        closeModal();
                    }
                });
            }
        }

        function updateTask() {
        if (editForm.id !== null && editForm.title !== '' && editForm.description !== '') {
            const dueDateTime = `${editForm.due_date}T${editForm.due_time}`;
            Inertia.put(`/tasks/${editForm.id}`, { ...editForm, due_date_time: dueDateTime }, {
                onSuccess: () => {
                    const index = tasks.value.findIndex(task => task.id === editForm.id);
                    if (index !== -1) {
                        tasks.value[index] = { ...tasks.value[index], ...editForm };
                    }
                    closeEditModal();
                }
            });
            }
        }

        function updateTaskStatus(task) {
            const originalCompleted = task.completed;

            // Optimistically update the local state
            const index = tasks.value.findIndex(t => t.id === task.id);
            if (index !== -1) {
                tasks.value[index].completed = task.completed;
            }

            Inertia.put(`/tasks/${task.id}`, { completed: task.completed }, {
                preserveState: true,
                onSuccess: () => {
                    // Optional: You can handle success logic here if needed
                },
                onError: () => {
                    // Revert the state in case of an error
                    if (index !== -1) {
                        tasks.value[index].completed = originalCompleted;
                    }
                }
            });
        }

        function calculateTimeRemaining(dueDate, dueTime) {
            const now = new Date();
            const due = new Date(`${dueDate}T${dueTime}`);
            const diff = due - now;
            if (diff <= 0) {
                return 'Vencido';
            }
            if(diff < 60000) {
                return 'Menos de 1 minuto restante';
            }
            if(diff < 3600000) {
                const minutes = Math.floor(diff / 60000);
                return `${minutes}m restantes`;
            }
            if(diff < 86400000) {
                const hours = Math.floor(diff / 3600000);
                const minutes = Math.floor((diff % 3600000) / 60000);
                return `${hours}h ${minutes}m restantes`;
            }
            if(diff < 604800000) {
                const days = Math.floor(diff / 86400000);
                const hours = Math.floor((diff % 86400000) / 3600000);
                return `${days}d ${hours}h restantes`;
            }
            if(diff < 2592000000) {
                const weeks = Math.floor(diff / 604800000);
                const days = Math.floor((diff % 604800000) / 86400000);
                return `${weeks}w ${days}d restantes`;
            }
            if(diff < 31536000000) {
                const months = Math.floor(diff / 2592000000);
                const weeks = Math.floor((diff % 2592000000) / 604800000);
                return `${months}m ${weeks}w restantes`;
            }
        }

        return {
            form,
            editForm,
            detailsForm,
            tasks,
            showModal,
            openModal,
            closeModal,
            openEditModal,
            showEditModal,
            closeEditModal,
            showDetailsModal,
            openDetailsModal,
            closeDetailsModal,
            submit,
            updateTask,
            updateTaskStatus,
            destroy,
            calculateTimeRemaining,
            flashMessage,
            currentPage,
            totalPages,
            paginatedTasks
        };
    },
    
};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 1s ease;
}
.fade-enter, .fade-leave-active {
    opacity: 0;
}
</style>