import { Head, useForm } from '@inertiajs/react';
import AdminLayout from '../../../Layouts/AdminLayout';
import CategoryForm from './Form';

export default function Create({ parents }) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        parent_id: '',
        description: '',
        position: 0,
        is_active: true,
    });

    function handleSubmit(e) {
        e.preventDefault();
        post(route('admin.categories.store'));
    }

    return (
        <AdminLayout title="Nouvelle catégorie">
            <Head title="Nouvelle catégorie — Administration" />
            <div className="admin-card max-w-[56rem]">
                <CategoryForm
                    data={data}
                    setData={setData}
                    errors={errors}
                    parents={parents}
                    processing={processing}
                    onSubmit={handleSubmit}
                    submitLabel="Créer"
                />
            </div>
        </AdminLayout>
    );
}
