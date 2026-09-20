import { Head, useForm } from '@inertiajs/react';
import AdminLayout from '../../../Layouts/AdminLayout';
import ProducerForm from './Form';

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        region: '',
        description: '',
        photo: null,
        is_featured: false,
        is_active: true,
    });

    function handleSubmit(e) {
        e.preventDefault();
        post(route('admin.producteurs.store'));
    }

    return (
        <AdminLayout title="Nouveau producteur">
            <Head title="Nouveau producteur — Administration" />
            <div className="admin-card max-w-[56rem]">
                <ProducerForm
                    data={data}
                    setData={setData}
                    errors={errors}
                    processing={processing}
                    onSubmit={handleSubmit}
                    submitLabel="Créer"
                />
            </div>
        </AdminLayout>
    );
}
