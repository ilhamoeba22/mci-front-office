<template>
    <div class="user-management">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white transition-colors">
                Manajemen Staff
            </h2>
            <button @click="openModal()" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg transition-all flex items-center gap-2 shadow-lg hover:shadow-blue-500/20">
                <i class="fa-solid fa-user-plus"></i> Tambah Staff
            </button>
        </div>

        <!-- Table Card -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden transition-colors">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-slate-900/50 border-b border-gray-200 dark:border-slate-700">
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Nama & Username</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Counter No</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Rating (IKM)</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                        <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold">
                                        {{ user.name.charAt(0) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800 dark:text-white">{{ user.name }}</div>
                                        <div class="text-xs text-gray-500 font-mono">{{ user.username }} / {{ user.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase border"
                                      :class="roleBadge(user.role)">
                                    {{ user.role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-slate-300 font-mono font-bold">
                                {{ user.counter_no ? 'LOKET ' + user.counter_no : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="flex text-yellow-500 text-[10px]">
                                        <i v-for="i in 5" :key="i" :class="['fa-solid fa-star', i <= Math.round(user.average_rating) ? '' : 'opacity-20']"></i>
                                    </div>
                                    <span class="text-xs font-black text-gray-800 dark:text-white">{{ user.average_rating }}</span>
                                    <span class="text-[10px] text-gray-400">({{ user.surveys_count }} survey)</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span v-if="user.is_active" class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 text-xs font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Aktif
                                </span>
                                <span v-else class="flex items-center gap-1.5 text-rose-600 dark:text-rose-400 text-xs font-bold opacity-60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Nonaktif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button @click="openModal(user)" class="p-2 text-indigo-600 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-indigo-500/10 rounded-lg transition-all" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button @click="deleteUser(user)" class="p-2 text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-500/10 rounded-lg transition-all" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="users.length === 0">
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">Belum ada data staff.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-all animate-fade-in">
            <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-md shadow-2xl border border-gray-100 dark:border-slate-700 overflow-hidden transform animate-scale-up">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-gray-50 dark:bg-slate-900/50">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                        {{ editingUser ? 'Edit Staff' : 'Tambah Staff Baru' }}
                    </h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <form @submit.prevent="saveUser" class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Nama Lengkap</label>
                            <input v-model="form.name" type="text" required class="w-full bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white rounded-lg p-2.5 outline-none focus:border-blue-500 transition-colors" placeholder="Masukkan nama...">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Username</label>
                            <input v-model="form.username" type="text" required class="w-full bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white rounded-lg p-2.5 outline-none focus:border-blue-500 transition-colors" placeholder="user123">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Nomor Loket</label>
                            <input v-model="form.counter_no" type="number" class="w-full bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white rounded-lg p-2.5 outline-none focus:border-blue-500 transition-colors" placeholder="1">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Email</label>
                        <input v-model="form.email" type="email" required class="w-full bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white rounded-lg p-2.5 outline-none focus:border-blue-500 transition-colors" placeholder="staff@bankmci.id">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">
                            Password {{ editingUser ? '(Kosongkan jika tidak diubah)' : '' }}
                        </label>
                        <input v-model="form.password" type="password" :required="!editingUser" class="w-full bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white rounded-lg p-2.5 outline-none focus:border-blue-500 transition-colors" placeholder="••••••••">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Role</label>
                            <select v-model="form.role" required class="w-full bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white rounded-lg p-2.5 outline-none focus:border-blue-500 transition-colors">
                                <option value="teller">Teller</option>
                                <option value="cs">Customer Service</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <label class="flex items-center gap-2 cursor-pointer p-2.5">
                                <input v-model="form.is_active" type="checkbox" class="w-4 h-4 text-blue-600 rounded">
                                <span class="text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-tight">Status Aktif</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="button" @click="showModal = false" class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-all font-bold">
                            Batal
                        </button>
                        <button type="submit" :disabled="loading" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-500 disabled:opacity-50 transition-all font-bold shadow-lg shadow-blue-500/20">
                            {{ loading ? 'Menyimpan...' : 'Simpan Staff' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    data() {
        return {
            users: [],
            showModal: false,
            loading: false,
            editingUser: null,
            form: {
                name: '',
                username: '',
                email: '',
                password: '',
                role: 'teller',
                counter_no: '',
                is_active: true
            }
        }
    },
    mounted() {
        this.fetchUsers();
    },
    methods: {
        async fetchUsers() {
            try {
                const response = await axios.get('/admin/users-data');
                this.users = response.data;
            } catch (error) {
                console.error("Error fetching users:", error);
            }
        },
        openModal(user = null) {
            this.editingUser = user;
            if (user) {
                this.form = { ...user, password: '' };
            } else {
                this.form = {
                    name: '',
                    username: '',
                    email: '',
                    password: '',
                    role: 'teller',
                    counter_no: '',
                    is_active: true
                };
            }
            this.showModal = true;
        },
        async saveUser() {
            this.loading = true;
            try {
                if (this.editingUser) {
                    await axios.put(`/admin/users-data/${this.editingUser.id}`, this.form);
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Data diperbarui', showConfirmButton: false, timer: 2000 });
                } else {
                    await axios.post('/admin/users-data', this.form);
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Staff ditambahkan', showConfirmButton: false, timer: 2000 });
                }
                this.showModal = false;
                this.fetchUsers();
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    text: error.response?.data?.message || 'Terjadi kesalahan sistem'
                });
            } finally {
                this.loading = false;
            }
        },
        async deleteUser(user) {
            const result = await Swal.fire({
                title: 'Hapus Staff?',
                text: `Akun ${user.name} akan dihapus secara permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Hapus!'
            });

            if (result.isConfirmed) {
                try {
                    await axios.delete(`/admin/users-data/${user.id}`);
                    this.fetchUsers();
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Data dihapus', showConfirmButton: false, timer: 2000 });
                } catch (error) {
                    Swal.fire({ icon: 'error', title: 'Gagal Hapus', text: error.response?.data?.message });
                }
            }
        },
        roleBadge(role) {
            switch (role) {
                case 'admin': return 'bg-purple-100 text-purple-700 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/30';
                case 'teller': return 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30';
                case 'cs': return 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/30';
                default: return 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/30';
            }
        }
    }
}
</script>

<style scoped>
@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes scale-up {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
.animate-fade-in { animation: fade-in 0.2s ease-out; }
.animate-scale-up { animation: scale-up 0.2s cubic-bezier(0.16, 1, 0.3, 1); }
</style>
