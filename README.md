Asumsi dan Penjelasan
=====================
- Diasumsikan saat pertama kali membuat user baru akan berikut dengan satu alamat, 
  alamat ini akan secara otomatis "preferred" (1), dan user yang baru dibuat akan 
  berstatus 'active'

- Saat sebuah alamat user dirubah menjadi "preferred" (1) maka secara otomatis se-
  mua alamat lain yg mereferensi ke user tersebut akan menjadi "not-preferred" (0)

- Diasumsikan untuk menghapus user akan men-Delete recordnya, bukan hanya merubah 
  status "active" menjadi "archived"

- Menghapus user akan menghapus semua alamat yg mereferensi ke user tersebut

- Paging digunakan seperti ini domain/API/rec/(record per page)/(page number)
