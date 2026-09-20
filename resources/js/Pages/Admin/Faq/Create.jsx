import { Head, useForm } from '@inertiajs/react';
import AdminLayout from '../../../Layouts/AdminLayout';
import FaqForm from './Form';

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({
        question: '',
        answer: '',
        keywords: '',
        category: '',
        position: 0,
        is_active: true,
    });

    function handleSubmit(e) {
        e.preventDefault();
        post(route('admin.messagerie.faq.store'));
    }

    return (
        <AdminLayout title="Nouvelle question">
            <Head title="Nouvelle question — FAQ" />
            <div className="admin-card max-w-[48rem]">
                <FaqForm data={data} setData={setData} errors={errors} processing={processing} onSubmit={handleSubmit} submitLabel="Enregistrer" />
            </div>
        </AdminLayout>
    );
}
