import { ref } from 'vue'

// Status menu samping (drawer) admin di HP. Dipakai bersama oleh
// Topbar (tombol buka), Sidebar (panel), dan AdminLayout (latar gelap).
// Di desktop (>= lg) nilai ini tidak dipakai.
export const mobileNavOpen = ref(false)