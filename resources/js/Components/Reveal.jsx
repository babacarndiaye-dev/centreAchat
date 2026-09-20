import { motion } from 'framer-motion';

export default function Reveal({ children, delay = 0, y = 24, className, as = 'div', once = true, ...props }) {
    const Component = motion[as] ?? motion.div;

    return (
        <Component
            initial={{ opacity: 0, y }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once, margin: '-80px' }}
            transition={{ duration: 0.6, delay, ease: [0.16, 1, 0.3, 1] }}
            className={className}
            {...props}
        >
            {children}
        </Component>
    );
}
