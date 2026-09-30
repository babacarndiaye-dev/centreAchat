import { useEffect, useState } from 'react';
import { usePage } from '@inertiajs/react';
import { AnimatePresence, motion } from 'framer-motion';
import SidebarNav from '../Components/Admin/SidebarNav';
import PageTransition from '../Components/PageTransition';
import PullToRefresh from '../Components/PullToRefresh';
import NotificationBells from '../Components/Admin/NotificationBells';

function PushNotificationButton({ vapidPublicKey }) {
    const [status, setStatus] = useState('unsupported');

    async function refresh() {
        setStatus(window.DiabaHotelPush ? await window.DiabaHotelPush.status() : 'unsupported');
    }

    useEffect(() => {
        const timeout = setTimeout(() => refresh(), 300);
        return () => clearTimeout(timeout);
    }, []);

    async function toggle() {
        if (!window.DiabaHotelPush) return;
        if (status === 'subscribed') {
            await window.DiabaHotelPush.unsubscribe();
        } else {
            await window.DiabaHotelPush.subscribe(vapidPublicKey);
        }
        await refresh();
    }

    if (status === 'unsupported') return null;

    return (
        <button
            onClick={toggle}
            type="button"
            className={'relative text-xl transition ' + (status === 'subscribed' ? 'text-terroir-green' : 'text-terroir-dark/50')}
            aria-label={status === 'subscribed' ? 'Désactiver les notifications push' : 'Activer les notifications push'}
            title={status === 'subscribed' ? 'Notifications push activées' : 'Activer les notifications push'}
        >
            <span className="material-symbols-outlined">notifications</span>
            {status === 'subscribed' && (
                <span className="absolute -right-0.5 -top-0.5 h-2 w-2 rounded-full bg-terroir-green ring-2 ring-white" />
            )}
        </button>
    );
}

function FlashBanner() {
    const { props } = usePage();
    const [banner, setBanner] = useState(null);

    useEffect(() => {
        if (props.flash?.success) {
            setBanner({ type: 'success', text: props.flash.success });
        } else if (props.flash?.error) {
            setBanner({ type: 'error', text: props.flash.error });
        }
    }, [props.flash?.success, props.flash?.error]);

    return (
        <AnimatePresence>
            {banner && (
                <motion.div
                    initial={{ opacity: 0, y: -8 }}
                    animate={{ opacity: 1, y: 0 }}
                    exit={{ opacity: 0, y: -8 }}
                    transition={{ duration: 0.2 }}
                    className={
                        'mx-6 mt-4 flex items-center justify-between gap-3 rounded-[2px] px-4 py-3 text-sm font-medium ' +
                        (banner.type === 'success' ? 'bg-terroir-green/10 text-terroir-green' : 'bg-terroir-terracotta/10 text-terroir-terracotta')
                    }
                >
                    {banner.text}
                    <button onClick={() => setBanner(null)} className="opacity-60 hover:opacity-100">
                        <span className="material-symbols-outlined text-lg">close</span>
                    </button>
                </motion.div>
            )}
        </AnimatePresence>
    );
}

export default function AdminLayout({ title, children }) {
    const { props } = usePage();
    const user = props.auth?.user;
    const errors = props.errors ?? {};
    const errorList = Object.values(errors);
    const vapidPublicKey = props.admin?.vapidPublicKey;
    const unreadChat = props.admin?.unreadChat ?? 0;
    const unreadNotifications = props.admin?.unreadNotifications ?? 0;
    const ordersPending = props.admin?.ordersPending ?? 0;
    const [mobileOpen, setMobileOpen] = useState(false);

    useEffect(() => {
        setMobileOpen(false);
    }, [props.site]);

    return (
        <div className="flex min-h-screen">
            <aside className="hidden w-64 shrink-0 flex-col bg-terroir-dark lg:flex">
                <SidebarNav />
            </aside>

            <AnimatePresence>
                {mobileOpen && (
                    <motion.div
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        className="fixed inset-0 z-40 lg:hidden"
                    >
                        <div className="absolute inset-0 bg-black/50" onClick={() => setMobileOpen(false)} />
                        <motion.div
                            initial={{ x: '-100%' }}
                            animate={{ x: 0 }}
                            exit={{ x: '-100%' }}
                            transition={{ duration: 0.2, ease: 'easeOut' }}
                            className="relative flex h-full w-64 flex-col bg-terroir-dark"
                        >
                            <SidebarNav />
                        </motion.div>
                    </motion.div>
                )}
            </AnimatePresence>

            <div className="flex min-w-0 flex-1 flex-col bg-terroir-cream text-terroir-dark">
                <header className="flex h-16 shrink-0 items-center justify-between border-b border-terroir-dark/10 bg-white px-6">
                    <button onClick={() => setMobileOpen(true)} type="button" className="text-xl text-terroir-dark lg:hidden" aria-label="Menu">
                        <span className="material-symbols-outlined">menu</span>
                    </button>
                    <h1 className="font-display text-lg font-semibold">{title ?? 'Tableau de bord'}</h1>
                    <div className="flex items-center gap-4">
                        {vapidPublicKey && <PushNotificationButton vapidPublicKey={vapidPublicKey} />}
                        <NotificationBells
                            initialUnreadChat={unreadChat}
                            initialUnreadNotifications={unreadNotifications}
                            initialOrdersPending={ordersPending}
                        />
                        <span className="hidden text-sm text-terroir-dark/60 sm:inline">{user?.name}</span>
                    </div>
                </header>

                <FlashBanner />

                {errorList.length > 0 && (
                    <div className="mx-6 mt-4 rounded-[2px] bg-terroir-terracotta/10 px-4 py-3 text-sm text-terroir-terracotta">
                        <ul className="list-inside list-disc space-y-0.5">
                            {errorList.map((error, i) => <li key={i}>{error}</li>)}
                        </ul>
                    </div>
                )}

                <main className="flex-1 p-6">
                    <PullToRefresh>
                        <PageTransition>{children}</PageTransition>
                    </PullToRefresh>
                </main>
            </div>
        </div>
    );
}
