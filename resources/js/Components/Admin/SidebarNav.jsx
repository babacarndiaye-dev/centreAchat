import { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { AnimatePresence, motion } from 'framer-motion';
import { isAdminInertiaRoute } from '../../Support/adminInertiaRoutes';

function routeBase(name) {
    const idx = name.lastIndexOf('.');
    return idx === -1 ? name : name.slice(0, idx);
}

function NavGroup({ group, unreadChat, currentRoute }) {
    const isLinkActive = (link) => currentRoute
        && (currentRoute === link.route || routeBase(currentRoute) === routeBase(link.route));
    const groupActive = group.items.some(isLinkActive);
    const [open, setOpen] = useState(groupActive);

    return (
        <div>
            <button
                type="button"
                onClick={() => setOpen((v) => !v)}
                className="flex w-full items-center justify-between rounded-[2px] px-3 py-2 text-left text-[11px] font-bold uppercase tracking-wider text-white/40 transition hover:text-white/70"
            >
                <span>{group.label}</span>
                <motion.span
                    animate={{ rotate: open ? 180 : 0 }}
                    transition={{ duration: 0.2 }}
                    className="material-symbols-outlined text-sm"
                >
                    expand_more
                </motion.span>
            </button>
            <AnimatePresence initial={false}>
                {open && (
                    <motion.ul
                        initial={{ height: 0, opacity: 0 }}
                        animate={{ height: 'auto', opacity: 1 }}
                        exit={{ height: 0, opacity: 0 }}
                        transition={{ duration: 0.2 }}
                        className="mt-0.5 space-y-0.5 overflow-hidden"
                    >
                        {group.items.map((link) => {
                            const active = isLinkActive(link);
                            const className =
                                'flex items-center gap-2.5 rounded-[2px] px-3 py-2 text-sm transition ' +
                                (active ? 'bg-terroir-green text-white' : 'text-white/65 hover:bg-white/5 hover:text-white');
                            const content = (
                                <>
                                    <span className="material-symbols-outlined text-lg">{link.icon}</span>
                                    <span>{link.label}</span>
                                    {link.route === 'admin.messagerie.index' && unreadChat > 0 && (
                                        <span className="ml-auto rounded-full bg-terroir-gold px-1.5 py-0.5 text-[10px] font-bold text-terroir-dark">
                                            {unreadChat}
                                        </span>
                                    )}
                                </>
                            );
                            return (
                                <li key={link.route}>
                                    {isAdminInertiaRoute(link.route) ? (
                                        <Link href={route(link.route)} className={className}>
                                            {content}
                                        </Link>
                                    ) : (
                                        <a href={route(link.route)} className={className}>
                                            {content}
                                        </a>
                                    )}
                                </li>
                            );
                        })}
                    </motion.ul>
                )}
            </AnimatePresence>
        </div>
    );
}

export default function SidebarNav() {
    const { props } = usePage();
    const groups = props.admin?.nav ?? [];
    const unreadChat = props.admin?.unreadChat ?? 0;
    const siteName = props.site?.name ?? "DIABA HOTEL";
    const logoUrl = props.site?.logoUrl;
    const currentRoute = route().current();

    return (
        <>
            <div className="flex h-16 shrink-0 items-center gap-2 border-b border-white/10 px-6">
                <img src={logoUrl || '/images/logo-white.svg'} alt="" className="h-9 w-auto object-contain" />
                <span className="font-display text-xl uppercase leading-none tracking-wider text-white">{siteName}</span>
            </div>

            <nav className="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                {groups.map((group) => (
                    <NavGroup key={group.label} group={group} unreadChat={unreadChat} currentRoute={currentRoute} />
                ))}
            </nav>

            <div className="shrink-0 space-y-0.5 border-t border-white/10 p-3">
                <a href={route('accueil')} className="flex items-center gap-2.5 rounded-[2px] px-3 py-2 text-sm text-white/65 hover:bg-white/5 hover:text-white">
                    <span className="material-symbols-outlined text-lg">arrow_back</span>
                    Retour au site
                </a>
                <form action={route('logout')} method="POST">
                    <input type="hidden" name="_token" value={document.querySelector('meta[name=csrf-token]')?.content} />
                    <button type="submit" className="flex w-full items-center gap-2.5 rounded-[2px] px-3 py-2 text-left text-sm text-white/65 hover:bg-white/5 hover:text-white">
                        <span className="material-symbols-outlined text-lg">logout</span>
                        Se déconnecter
                    </button>
                </form>
            </div>
        </>
    );
}
