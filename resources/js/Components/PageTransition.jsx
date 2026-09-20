import { AnimatePresence, motion } from 'framer-motion';
import { usePage } from '@inertiajs/react';

export default function PageTransition({ children, className }) {
    const { url } = usePage();

    return (
        <AnimatePresence mode="wait" initial={false}>
            <motion.div
                key={url}
                className={className}
                initial={{ opacity: 0, y: 8 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -8 }}
                transition={{ duration: 0.18, ease: 'easeOut' }}
            >
                {children}
            </motion.div>
        </AnimatePresence>
    );
}
