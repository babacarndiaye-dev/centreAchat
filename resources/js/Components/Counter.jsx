import { useEffect, useRef } from 'react';
import { animate } from 'framer-motion';

export default function Counter({ value, format = (v) => Math.round(v).toLocaleString('fr-FR'), duration = 0.9, delay = 0 }) {
    const nodeRef = useRef(null);

    useEffect(() => {
        const node = nodeRef.current;
        const controls = animate(0, value, {
            duration,
            delay,
            ease: [0.16, 1, 0.3, 1],
            onUpdate: (v) => { node.textContent = format(v); },
        });
        return () => controls.stop();
    }, [value]);

    return <span ref={nodeRef}>{format(0)}</span>;
}
