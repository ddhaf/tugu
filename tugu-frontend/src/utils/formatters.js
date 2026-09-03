const INDONESIAN_MONTHS = [
  'Januari',
  'Februari',
  'Maret',
  'April',
  'Mei',
  'Juni',
  'Juli',
  'Agustus',
  'September',
  'Oktober',
  'November',
  'Desember',
]

const INDONESIAN_MONTHS_SHORT = [
  'Jan',
  'Feb',
  'Mar',
  'Apr',
  'Mei',
  'Jun',
  'Jul',
  'Agu',
  'Sep',
  'Okt',
  'Nov',
  'Des',
]

/**
 * Format a number as Indonesian Rupiah.
 * @param {number|string} amount
 * @returns {string} e.g. "Rp 1.500.000"
 */
export function formatRupiah(amount) {
  const num = Number(amount)
  if (isNaN(num)) return 'Rp 0'

  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(num)
}

/**
 * Format a date string to Indonesian format: "14 Agustus 2026"
 * @param {string} dateString
 * @returns {string}
 */
export function formatDate(dateString) {
  if (!dateString) return '-'
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return '-'

  const day = date.getDate()
  const month = INDONESIAN_MONTHS[date.getMonth()]
  const year = date.getFullYear()

  return `${day} ${month} ${year}`
}

/**
 * Format a date string to short Indonesian: "14 Agu 2026"
 * @param {string} dateString
 * @returns {string}
 */
export function formatDateShort(dateString) {
  if (!dateString) return '-'
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return '-'

  const day = date.getDate()
  const month = INDONESIAN_MONTHS_SHORT[date.getMonth()]
  const year = date.getFullYear()

  return `${day} ${month} ${year}`
}

/**
 * Format a date string with time: "14 Agustus 2026, 09:30"
 * @param {string} dateString
 * @returns {string}
 */
export function formatDateTime(dateString) {
  if (!dateString) return '-'
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return '-'

  const day = date.getDate()
  const month = INDONESIAN_MONTHS[date.getMonth()]
  const year = date.getFullYear()
  const hours = String(date.getHours()).padStart(2, '0')
  const minutes = String(date.getMinutes()).padStart(2, '0')

  return `${day} ${month} ${year}, ${hours}:${minutes}`
}
