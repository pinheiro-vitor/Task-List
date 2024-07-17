<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex align-middle justify-center h-full p-2">
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight uppercase">Task List</h2>
            </div>
        </template>

        <div class="mt-4 min-w-full flex items-center justify-center bg-teal-lightest font-sans">
            <div class="bg-gray-250 rounded shadow p-6 m-4 w-full">
                <div class="mb-2 flex flex-row justify-between align-middle">
                    <button class="uppercase text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"@click="openModal">
                        Adicionar task
                    </button>
                    <form class="max-w-md">
                        <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input type="search" v-model="searchQuery" @keyup.enter="searchTasks" id="default-search" class="block w-full px-8 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Titulo ou descrição" required />
                        </div>
                    </form>
                </div>
                <transition name="fade">
                    <div v-if="flashMessage" class="center absolute bg-blue-100 text-blue-600 p-1 rounded shadow-lg">
                        {{ flashMessage }}
                    </div>
                </transition>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="tracking-wider text-xs px-6 py-4 text-center text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">ID</th>
                                <th scope="col" class="px-6 py-3">Título</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3">Prazo</th>
                                <th scope="col" class="px-6 py-3">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600" v-for="task in paginatedTasks" :key="task.id">
                                <td class="px-6 py-4 text-center text-white tracking-wider">{{ task.id }}</td>
                                <td class="px-6 py-4 text-center text-white tracking-wider">{{ task.title }}</td>
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" v-model="task.completed" @change="updateTaskStatus(task)" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                </td>
                                <td class="px-6 py-4 text-center text-white tracking-wider">{{ calculateTimeRemaining(task.due_date, task.due_time) }}</td>
                                <td class="px-6 py-4 text-center text-white tracking-wider">
                                    <button @click="openDetailsModal(task)" class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
                                        Visualizar
                                    </button>
                                    <button @click="openEditModal(task)" class="text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800">
                                        Editar
                                    </button>
                                    <button @click="destroy(task.id)" type="button" class="text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
                                        Apagar
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Paginação -->
                <div class="mt-4 flex justify-between items-center">
                    <button @click="downloadCSV" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1 px-3 rounded inline-flex items-center">
                        <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/></svg>
                        <span>Exportar CSV</span>
                    </button>
                    <nav aria-label="Page navigation example">
                        <ul class="flex items-center -space-x-px h-8 text-sm">
                            <li>
                                <button class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white" :disabled="currentPage === 1" @click="currentPage--">
                                    <span class="sr-only">Anterior</span>
                                    <svg class="w-2.5 h-2.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4" />
                                    </svg>
                                </button>
                            </li>
                            <li v-for="page in totalPages" :key="page">
                                <button @click="currentPage = page" :class="{
                                    'z-10 flex items-center justify-center px-3 h-8 leading-tight text-blue-600 border border-blue-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white': currentPage === page,
                                    'flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white': currentPage !== page
                                }">
                                    {{ page }}
                                </button>
                            </li>
                            <li>
                                <button class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white" :disabled="currentPage === totalPages" @click="currentPage++">
                                    <span class="sr-only">Próxima</span>
                                    <svg class="w-2.5 h-2.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                                    </svg>
                                </button>
                            </li>
                        </ul>
                    </nav>
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
                                <p v-if="form.errors.title" class="text-red-500 text-sm">{{ form.errors.title }}</p>
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker mt-4" placeholder="Description" v-model="form.description">
                                <p v-if="form.errors.description" class="text-red-500 text-sm">{{ form.errors.description }}</p>
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker mt-4" type="date" placeholder="Data de Vencimento" v-model="form.due_date">
                                <p v-if="form.errors.due_date" class="text-red-500 text-sm">{{ form.errors.due_date }}</p>
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker mt-4" type="time" placeholder="Hora de Vencimento" v-model="form.due_time">
                                <p v-if="form.errors.due_date_time" class="text-red-500 text-sm">{{ form.errors.due_date_time }}</p>
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
                                <p v-if="editForm.errors.title" class="text-red-500 text-sm">{{ editForm.errors.title }}</p>
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker mt-4" placeholder="Description" v-model="editForm.description">
                                <p v-if="editForm.errors.description" class="text-red-500 text-sm">{{ editForm.errors.description }}</p>
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker mt-4" type="date" placeholder="Data de Vencimento" v-model="editForm.due_date">
                                <p v-if="editForm.errors.due_date" class="text-red-500 text-sm">{{ editForm.errors.due_date }}</p>
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker mt-4" type="time" placeholder="Hora de Vencimento" v-model="editForm.due_time">
                                <p v-if="editForm.errors.due_date_time" class="text-red-500 text-sm">{{ editForm.errors.due_date_time }}</p>
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
    methods: {
        downloadCSV() {
            window.location.href = this.route('tasks.export');
        },
    },
    props: {
        tasks: Array,
        flash: Object
    },
    setup(props) {
        const currentPage = ref(1);
        const itemsPerPage = 5;

        const totalPages = computed(() => Math.ceil(filteredTasks.value.length / itemsPerPage));
        const paginatedTasks = computed(() => {
            const startIndex = (currentPage.value - 1) * itemsPerPage;
            return filteredTasks.value.slice(startIndex, startIndex + itemsPerPage);
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
            due_time: '',
            errors: {}
        });

        const editForm = reactive({
            id: null,
            title: '',
            description: '',
            due_date: '',
            due_time: '',
            errors: {}
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
        const searchQuery = ref('');

        const filteredTasks = computed(() => {
            return tasks.value.filter(task => 
                task.title.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                task.description.toLowerCase().includes(searchQuery.value.toLowerCase())
            );
        });

        onMounted(() => {
            if (flashMessage.value) {
                setTimeout(() => {
                    flashMessage.value = '';
                }, 2200);
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
            editForm.errors = {};
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

        function validateForm() {
            const errors = {};
            const now = new Date();
            const dueDateTime = new Date(`${form.due_date}T${form.due_time}`);

            if (form.title.trim() === '') {
                errors.title = 'O título não pode ficar em branco.';
            }

            if (form.description.trim() === '') {
                errors.description = 'A descrição não pode ficar em branco.';
            } else if (form.description.length < 3) {
                errors.description = 'A descrição deve ter pelo menos 3 caracteres.';
            }

            if ((form.due_date && !form.due_time) || (!form.due_date && form.due_time)) {
                errors.due_date_time = 'Se a data ou hora estiver preenchida, ambos devem estar preenchidos.';
            }

            if (form.due_date && form.due_time && dueDateTime <= now) {
                errors.due_date = 'A data e a hora devem ser no futuro.';
            }

            form.errors = errors;
            return Object.keys(errors).length === 0;
        }

        function validateEditForm() {
            const errors = {};
            const now = new Date();
            const dueDateTime = new Date(`${editForm.due_date}T${editForm.due_time}`);

            if (editForm.title.trim() === '') {
                errors.title = 'O título não pode ficar em branco.';
            }

            if (editForm.description.trim() === '') {
                errors.description = 'A descrição não pode ficar em branco.';
            } else if (editForm.description.length < 3) {
                errors.description = 'A descrição deve ter pelo menos 3 caracteres.';
            }

            if ((editForm.due_date && !editForm.due_time) || (!editForm.due_date && editForm.due_time)) {
                errors.due_date_time = 'Se a data ou hora estiver preenchida, ambos devem estar preenchidos.';
            }

            if (editForm.due_date && editForm.due_time && dueDateTime <= now) {
                errors.due_date = 'A data e a hora devem ser no futuro.';
            }

            editForm.errors = errors;
            return Object.keys(errors).length === 0;
        }
        

        function submit() {
            if (validateForm()) {
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
            if (validateEditForm() && editForm.id !== null) {
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

            const index = tasks.value.findIndex(t => t.id === task.id);
            if (index !== -1) {
                tasks.value[index].completed = task.completed;
            }

            Inertia.put(`/tasks/${task.id}`, { completed: task.completed }, {
                preserveState: true,
                onSuccess: () => {},
                onError: () => {
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
                return 'Menos de 1 minuto';
            }
            if(diff < 3600000) {
                const minutes = Math.floor(diff / 60000);
                return `${minutes}m`;
            }
            if(diff < 86400000) {
                const hours = Math.floor(diff / 3600000);
                const minutes = Math.floor((diff % 3600000) / 60000);
                return `${hours}h ${minutes}m`;
            }
            if(diff < 604800000) {
                const days = Math.floor(diff / 86400000);
                const hours = Math.floor((diff % 86400000) / 3600000);
                return `${days}d ${hours}h`;
            }
            if(diff < 2592000000) {
                const weeks = Math.floor(diff / 604800000);
                const days = Math.floor((diff % 604800000) / 86400000);
                return `${weeks}w ${days}d`;
            }
            if(diff < 31536000000) {
                const months = Math.floor(diff / 2592000000);
                const weeks = Math.floor((diff % 2592000000) / 604800000);
                return `${months}m ${weeks}w`;
            }
        }

        function searchTasks() {
            Inertia.get('/tasks', { search: searchQuery.value });
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
            paginatedTasks,
            searchQuery,
            searchTasks,
            validateEditForm,
            validateForm
        };
    }
};
</script>
