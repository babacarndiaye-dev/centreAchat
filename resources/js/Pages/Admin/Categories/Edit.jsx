import { Head, useForm } from '@inertiajs/react';
import AdminLayout from '../../../Layouts/AdminLayout';
import CategoryForm from './Form';

export default function Edit({ category, parents }) {
    const { data, setData, put, processing, errors } = useForm({
        name: category.name,
        parent_id: category.parent_id ?? '',
        description: category.description ?? '',
        position: category.position ?? 0,
        is_active: category.is_active,
    });

    function handleSubmit(e) {
        e.preventDefault();
        put(route('admin.categories.update', category.id));
    }

    return (
        <AdminLayout title="Modifier la catégorie">
            <Head title="Modifier la catégorie — Administration" />
            <div className="admin-card max-w-[56rem]">
                <CategoryForm
                    data={data}
                    setData={setData}
                    errors={errors}
                    parents={parents}
                    processing={processing}
                    onSubmit={handleSubmit}
                    submitLabel="Enregistrer"
                />
            </div>
        </AdminLayout>
    );
}
