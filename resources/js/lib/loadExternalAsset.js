/**
 * Loader kecil untuk memuat library eksternal (Leaflet, face-api.js) lewat
 * CDN saat runtime di browser, tanpa perlu ditambahkan ke package.json /
 * npm install. Cocok untuk fitur yang "menempel" seperti peta & face
 * recognition supaya tidak menggemukkan bundle utama aplikasi.
 *
 * Setiap URL hanya di-load sekali walau dipanggil berkali-kali dari
 * beberapa komponen (di-cache pakai Promise).
 */

const loadedScripts = new Map()
const loadedStyles = new Set()

export function loadScript(src) {
    if (loadedScripts.has(src)) {
        return loadedScripts.get(src)
    }

    const promise = new Promise((resolve, reject) => {
        const script = document.createElement('script')
        script.src = src
        script.async = true
        script.onload = () => resolve()
        script.onerror = () => reject(new Error(`Gagal memuat script: ${src}`))
        document.head.appendChild(script)
    })

    loadedScripts.set(src, promise)
    return promise
}

export function loadStyle(href) {
    if (loadedStyles.has(href)) {
        return
    }

    const link = document.createElement('link')
    link.rel = 'stylesheet'
    link.href = href
    document.head.appendChild(link)

    loadedStyles.add(href)
}