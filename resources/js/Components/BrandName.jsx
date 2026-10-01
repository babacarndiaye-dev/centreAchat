// Nom de marque affiché sur deux lignes : « DIABA HOTEL » en grand, puis
// « Produits du Sénégal (D.H.P.S) » en petit. Garde l'en-tête sur une seule ligne
// malgré la longueur du nom complet.
export function splitBrand(name = '') {
    const index = name.search(/\sProduits du S/i);

    return index > 0 ? [name.slice(0, index), name.slice(index + 1)] : [name, ''];
}

const SIZES = {
    sm: { main: 'text-lg', sub: 'text-[8px]' },
    md: { main: 'text-2xl', sub: 'text-[9px]' },
    lg: { main: 'text-3xl', sub: 'text-[10px]' },
};

const TONES = {
    green: { main: 'text-terroir-green', sub: 'text-terroir-dark/70' },
    light: { main: 'text-white', sub: 'text-white/70' },
};

export default function BrandName({ name, tone = 'green', size = 'md', className = '' }) {
    const [main, sub] = splitBrand(name);
    const sizes = SIZES[size] ?? SIZES.md;
    const tones = TONES[tone] ?? TONES.green;

    return (
        <span className={`flex flex-col whitespace-nowrap leading-none ${className}`}>
            <span className={`font-display uppercase tracking-wider ${sizes.main} ${tones.main}`}>{main}</span>
            {sub && <span className={`mt-1 font-semibold uppercase tracking-[0.1em] ${sizes.sub} ${tones.sub}`}>{sub}</span>}
        </span>
    );
}
