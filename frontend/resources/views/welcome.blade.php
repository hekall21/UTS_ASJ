<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>UTS ASJ - User Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-slate-50 p-10 font-sans">
    <div class="max-w-5xl mx-auto">
        <h1 class="text-3xl font-black text-center mb-10 text-indigo-900">USER MANAGEMENT CLOUD</h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="bg-white p-6 rounded-2xl shadow-xl border border-indigo-100">
                <h2 id="formTitle" class="text-xl font-bold mb-5">Tambah User</h2>
                <form id="userForm" class="space-y-4">
                    <input type="hidden" id="userId">
                    <input type="text" id="name" placeholder="Nama Lengkap" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required>
                    <input type="email" id="email" placeholder="Email" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required>
                    <input type="file" id="photo" accept="image/*" class="w-full text-xs">
                    <p class="text-[10px] text-gray-400 italic">* Upload JPG/PNG Max 5MB</p>
                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg font-bold hover:bg-indigo-700 transition">SIMPAN</button>
                    <button type="button" id="cancelBtn" class="hidden w-full bg-gray-200 py-2 rounded-lg font-bold mt-2">BATAL</button>
                </form>
            </div>

            <div class="lg:col-span-2 bg-white rounded-2xl shadow-xl overflow-hidden border border-indigo-100">
                <table class="w-full text-left">
                    <thead class="bg-indigo-50">
                        <tr>
                            <th class="p-4 text-sm font-bold">User</th>
                            <th class="p-4 text-sm font-bold">Email</th>
                            <th class="p-4 text-sm font-bold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="userTable" class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const API_URL = 'http://localhost:8080/api/users';

        async function getUsers() {
            const res = await fetch(API_URL);
            const data = await res.json();
            const table = document.getElementById('userTable');
            table.innerHTML = '';
            data.forEach(u => {
                table.innerHTML += `
                    <tr>
                        <td class="p-4 flex items-center space-x-3">
                            <img src="${u.display_photo}" class="w-10 h-10 rounded-full object-cover">
                            <span class="font-medium">${u.name}</span>
                        </td>
                        <td class="p-4 text-sm text-gray-600">${u.email}</td>
                        <td class="p-4 text-right">
                            <button onclick="editUser(${u.id})" class="text-indigo-600 font-bold mr-3">Edit</button>
                            <button onclick="deleteUser(${u.id})" class="text-red-500 font-bold">Hapus</button>
                        </td>
                    </tr>`;
            });
        }

        document.getElementById('userForm').onsubmit = async (e) => {
            e.preventDefault();
            const id = document.getElementById('userId').value;
            const photoInput = document.getElementById('photo').files[0];
            const formData = new FormData();

            // Validasi File Gambar
            if (photoInput) {
                if (!['image/jpeg', 'image/png', 'image/jpg'].includes(photoInput.type)) {
                    return Swal.fire('Error', 'File harus JPG/PNG!', 'error');
                }
            }

            formData.append('name', document.getElementById('name').value);
            formData.append('email', document.getElementById('email').value);
            if (photoInput) formData.append('photo', photoInput);
            if (id) formData.append('_method', 'PUT');

            const url = id ? `${API_URL}/${id}` : API_URL;
            await fetch(url, { method: 'POST', body: formData });
            
            Swal.fire('Berhasil', 'Data diproses', 'success');
            resetForm();
            getUsers();
        };

        async function deleteUser(id) {
            const result = await Swal.fire({
                title: 'Hapus User?',
                text: "File di MinIO juga akan terhapus!",
                icon: 'warning',
                showCancelButton: true
            });

            if (result.isConfirmed) {
                await fetch(`${API_URL}/${id}`, { method: 'DELETE' });
                Swal.fire('Terhapus', 'Data dan file hilang!', 'success');
                getUsers();
            }
        }

        async function editUser(id) {
            const res = await fetch(`${API_URL}/${id}`);
            const u = await res.json();
            document.getElementById('userId').value = u.id;
            document.getElementById('name').value = u.name;
            document.getElementById('email').value = u.email;
            document.getElementById('formTitle').innerText = 'Edit User';
            document.getElementById('cancelBtn').classList.remove('hidden');
        }

        function resetForm() {
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('formTitle').innerText = 'Tambah User';
            document.getElementById('cancelBtn').classList.add('hidden');
        }

        document.getElementById('cancelBtn').onclick = resetForm;
        getUsers();
    </script>
</body>
</html>