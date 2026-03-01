import './bootstrap';
import Swal from 'sweetalert2';

window.Swal = Swal;
window.showAlert = function (type, title, text, options = {}) {
    const opts = { title: title || '', text: text || '', ...options };
    if (type === 'success') opts.icon = 'success';
    else if (type === 'error') opts.icon = 'error';
    else if (type === 'warning') opts.icon = 'warning';
    return Swal.fire(opts);
};
window.showConfirm = function (title, text, onConfirm, options = {}) {
    return Swal.fire({
        title: title || 'Are you sure?',
        text: text || '',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        ...options
    }).then((result) => {
        if (result.isConfirmed && typeof onConfirm === 'function') onConfirm();
        return result;
    });
};
