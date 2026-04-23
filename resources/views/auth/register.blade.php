@section('title', 'Daftar Akun - Sistem Peminjaman Buku')

<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Buat Akun Baru</h2>
        <p class="text-gray-500 mt-2 text-sm">Lengkapi data diri Anda untuk bergabung</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        {{-- Role Selection --}}
        <div class="bg-blue-50 p-4 rounded-xl mb-6">
            <x-input-label for="role" :value="__('Daftar Sebagai')" class="text-blue-800 font-bold mb-2" />
            <div class="relative">
                <select id="role" name="role" 
                    class="block w-full border-gray-200 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 rounded-lg transition-all" 
                    onchange="toggleFields()">
                    <option value="peminjam" {{ old('role') == 'peminjam' ? 'selected' : '' }}>Siswa (Peminjam)</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                </select>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Name & Email --}}
            <div class="md:col-span-2">
                <x-input-label for="name" :value="__('Nama Lengkap')" class="text-gray-700 font-medium ml-1" />
                <x-text-input id="name" class="block mt-1 w-full border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 py-2.5" type="text" name="name" :value="old('name')" required autofocus placeholder="Masukkan nama lengkap" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="md:col-span-2">
                <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-medium ml-1" />
                <x-text-input id="email" class="block mt-1 w-full border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 py-2.5" type="email" name="email" :value="old('email')" required placeholder="email@contoh.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- Fields for STUDENT (Hidden when Admin) --}}
            <div id="student_fields" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 transition-all">
                <div>
                    <x-input-label for="NISN" :value="__('NISN (10 Digit)')" class="text-gray-700 font-medium ml-1" />
                    <x-text-input id="NISN" class="block mt-1 w-full border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 py-2.5" type="text" name="NISN" :value="old('NISN')" maxlength="10" placeholder="0012345xxx" />
                    <x-input-error :messages="$errors->get('NISN')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="kelas_jurusan" :value="__('Kelas Jurusan')" class="text-gray-700 font-medium ml-1" />
                        <select id="kelas_jurusan" name="kelas_jurusan" class="block mt-1 w-full border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 py-2.5">
                            <option value="">Pilih Kelas</option>
                            @foreach([
                                /* Kelas 10 */
                                '10 PPLG 1', '10 PPLG 2', '10 PPLG 3', '10 BCF 1', '10 BCF 2', '10 ANM 1', '10 ANM 2', '10 TO 1', '10 TO 2', '10 TPFL 1', '10 TPFL 2',
                                /* Kelas 11 */
                                '11 PPLG 1', '11 PPLG 2', '11 PPLG 3', '11 BCF 1', '11 BCF 2', '11 ANM 1', '11 ANM 2', '11 TO 1', '11 TO 2', '11 TPFL 1', '11 TPFL 2',
                                /* Kelas 12 */
                                '12 PPLG 1', '12 PPLG 2', '12 PPLG 3', '12 BCF 1', '12 BCF 2', '12 ANM 1', '12 ANM 2', '12 TO 1', '12 TO 2', '12 TPFL 1', '12 TPFL 2'
                            ] as $kelas)
                                <option value="{{ $kelas }}" {{ old('kelas_jurusan') == $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                            @endforeach
                        </select>
                    <x-input-error :messages="$errors->get('kelas_jurusan')" class="mt-2" />
                </div>
            </div>

            {{-- Fields for ADMIN (Hidden when Student) --}}
            <div id="admin_fields" class="md:col-span-2 hidden">
                <div class="p-4 bg-red-50 border border-red-100 rounded-xl">
                    <x-input-label for="admin_key" :value="__('Kode Registrasi Admin')" class="text-red-800 font-bold ml-1" />
                    <x-text-input id="admin_key" class="block mt-1 w-full border-red-200 rounded-xl focus:ring-4 focus:ring-red-100 py-2.5" type="password" name="admin_key" placeholder="Masukkan kode rahasia admin" />
                    <p class="text-xs text-red-600 mt-2">*Dapatkan kode ini dari Koordinator Perpustakaan.</p>
                    <x-input-error :messages="$errors->get('admin_key')" class="mt-2" />
                </div>
            </div>

            {{-- Passwords --}}
            <div>
                <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-medium ml-1" />
                <x-text-input id="password" class="block mt-1 w-full border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 py-2.5" type="password" name="password" required placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" class="text-gray-700 font-medium ml-1" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 py-2.5" type="password" name="password_confirmation" required placeholder="••••••••" />
            </div>
        </div>

        <div class="pt-6">
            <button type="submit" class="w-full py-3.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-200 transition-all duration-300 active:scale-[0.98]">
                Daftar Akun
            </button>
            
            {{-- Navigasi Kembali ke Login --}}
            <div class="mt-6 text-center border-t border-gray-100 pt-6">
                <p class="text-sm text-gray-500">
                    Sudah memiliki akun? 
                    <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:text-blue-700 transition-colors ml-1">
                        Masuk di sini
                    </a>
                </p>
            </div>
        </div>
    </form>

    <script>
        function toggleFields() {
            const role = document.getElementById('role').value;
            const studentFields = document.getElementById('student_fields');
            const adminFields = document.getElementById('admin_fields');
            
            const nisnInput = document.getElementById('NISN');
            const kelasInput = document.getElementById('kelas_jurusan');
            const adminKeyInput = document.getElementById('admin_key');

            if (role === 'peminjam') {
                studentFields.classList.remove('hidden');
                adminFields.classList.add('hidden');
                // Set required attribute
                nisnInput.setAttribute('required', 'required');
                kelasInput.setAttribute('required', 'required');
                adminKeyInput.removeAttribute('required');
            } else {
                studentFields.classList.add('hidden');
                adminFields.classList.remove('hidden');
                // Remove required for student, add for admin
                nisnInput.removeAttribute('required');
                kelasInput.removeAttribute('required');
                adminKeyInput.setAttribute('required', 'required');
            }
        }

        // Jalankan fungsi saat halaman dimuat pertama kali
        document.addEventListener('DOMContentLoaded', toggleFields);
    </script>
</x-guest-layout>