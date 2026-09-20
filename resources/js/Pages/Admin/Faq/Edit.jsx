import { Head, useForm } from '@inertiajs/react';
import AdminLayout from '../../../Layouts/AdminLayout';
import FaqForm from './Form';

export default function Edit({ entry }) {
    const { data, setData, put, processing, errors } = useForm({
        question: entry.question,
        answer: entry.answer,
        keywords: entry.keywords ?? '',
        category: entry.category ?? '',
        position: entry.position ?? 0,
        is_active: entry.is_active,
    });

    function handleSubmit(e) {
        e.preventDefault();
        put(route('admin.messagerie.faq.update', entry.id));
    }

    return (
        <AdminLayout title="Modifier la question">
            <Head title="Modifier la question — FAQ" />
            <div className="admin-card max-w-[48rem]">
                <FaqForm data={data} setData={setData} errors={errors} processing={processing} onSubmit={handleSubmit} submitLabel="Enregistrer" />
            </div>
        </AdminLayout>
    );
}
