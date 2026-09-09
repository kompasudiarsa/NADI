/**
 * NADI RSBM - QR Code generator lokal
 * QR Version 1-L, byte mode, mask pattern 0.
 * Tidak membutuhkan CDN / internet.
 * Kapasitas maksimal: 17 byte UTF-8.
 */
(function (global) {
    'use strict';

    const SIZE = 21;
    const DATA_CODEWORDS = 19;
    const ECC_CODEWORDS = 7;

    function utf8Bytes(text) {
        if (typeof TextEncoder !== 'undefined') {
            return Array.from(new TextEncoder().encode(String(text)));
        }

        const encoded = unescape(encodeURIComponent(String(text)));
        const bytes = [];

        for (let i = 0; i < encoded.length; i++) {
            bytes.push(encoded.charCodeAt(i) & 0xff);
        }

        return bytes;
    }

    function gfTables() {
        const exp = new Array(512).fill(0);
        const log = new Array(256).fill(0);

        let x = 1;

        for (let i = 0; i < 255; i++) {
            exp[i] = x;
            log[x] = i;

            x <<= 1;

            if (x & 0x100) {
                x ^= 0x11d;
            }
        }

        for (let i = 255; i < 512; i++) {
            exp[i] = exp[i - 255];
        }

        return { exp, log };
    }

    const GF = gfTables();

    function gfMul(a, b) {
        if (a === 0 || b === 0) {
            return 0;
        }

        return GF.exp[GF.log[a] + GF.log[b]];
    }

    function polyMul(a, b) {
        const out = new Array(a.length + b.length - 1).fill(0);

        for (let i = 0; i < a.length; i++) {
            for (let j = 0; j < b.length; j++) {
                out[i + j] ^= gfMul(a[i], b[j]);
            }
        }

        return out;
    }

    function generatorPolynomial(ecCount) {
        let generator = [1];

        for (let i = 0; i < ecCount; i++) {
            generator = polyMul(
                generator,
                [1, GF.exp[i]]
            );
        }

        return generator;
    }

    function reedSolomon(data, ecCount) {
        const generator = generatorPolynomial(ecCount);
        let ecc = new Array(ecCount).fill(0);

        data.forEach(function (byte) {
            const factor = byte ^ ecc[0];

            ecc = ecc.slice(1);
            ecc.push(0);

            for (let i = 0; i < ecCount; i++) {
                ecc[i] ^= gfMul(
                    generator[i + 1],
                    factor
                );
            }
        });

        return ecc;
    }

    function pushBits(bits, value, length) {
        for (let i = length - 1; i >= 0; i--) {
            bits.push((value >> i) & 1);
        }
    }

    function createCodewords(text) {
        const bytes = utf8Bytes(text);

        if (bytes.length > 17) {
            throw new Error(
                'Kode reservasi terlalu panjang untuk QR lokal Version 1-L.'
            );
        }

        const bits = [];

        // Byte mode = 0100
        pushBits(bits, 0x4, 4);

        // Version 1-9 byte-mode character count = 8 bits.
        pushBits(bits, bytes.length, 8);

        bytes.forEach(function (byte) {
            pushBits(bits, byte, 8);
        });

        const bitLimit = DATA_CODEWORDS * 8;
        const terminatorLength = Math.min(
            4,
            bitLimit - bits.length
        );

        for (let i = 0; i < terminatorLength; i++) {
            bits.push(0);
        }

        while (bits.length % 8 !== 0) {
            bits.push(0);
        }

        const data = [];

        for (let i = 0; i < bits.length; i += 8) {
            let value = 0;

            for (let j = 0; j < 8; j++) {
                value = (value << 1) | bits[i + j];
            }

            data.push(value);
        }

        let padIndex = 0;
        const pads = [0xec, 0x11];

        while (data.length < DATA_CODEWORDS) {
            data.push(pads[padIndex % 2]);
            padIndex++;
        }

        return data.concat(
            reedSolomon(
                data,
                ECC_CODEWORDS
            )
        );
    }

    function bchDigit(value) {
        let digit = 0;

        while (value !== 0) {
            digit++;
            value >>>= 1;
        }

        return digit;
    }

    function bchTypeInfo(data) {
        const G15 = 0x537;
        const G15_MASK = 0x5412;

        let d = data << 10;

        while (
            bchDigit(d) - bchDigit(G15) >= 0
        ) {
            d ^= G15 << (
                bchDigit(d) - bchDigit(G15)
            );
        }

        return (
            ((data << 10) | d) ^
            G15_MASK
        );
    }

    function setupFinder(matrix, row, col) {
        for (let r = -1; r <= 7; r++) {
            if (
                row + r < 0 ||
                row + r >= SIZE
            ) {
                continue;
            }

            for (let c = -1; c <= 7; c++) {
                if (
                    col + c < 0 ||
                    col + c >= SIZE
                ) {
                    continue;
                }

                const dark =
                    (
                        r >= 0 &&
                        r <= 6 &&
                        (c === 0 || c === 6)
                    )
                    ||
                    (
                        c >= 0 &&
                        c <= 6 &&
                        (r === 0 || r === 6)
                    )
                    ||
                    (
                        r >= 2 &&
                        r <= 4 &&
                        c >= 2 &&
                        c <= 4
                    );

                matrix[row + r][col + c] =
                    Boolean(dark);
            }
        }
    }

    function createMatrix(text) {
        const matrix = Array.from(
            { length: SIZE },
            function () {
                return new Array(SIZE).fill(null);
            }
        );

        setupFinder(matrix, 0, 0);
        setupFinder(matrix, SIZE - 7, 0);
        setupFinder(matrix, 0, SIZE - 7);

        // Timing patterns.
        for (let r = 8; r < SIZE - 8; r++) {
            if (matrix[r][6] === null) {
                matrix[r][6] =
                    r % 2 === 0;
            }
        }

        for (let c = 8; c < SIZE - 8; c++) {
            if (matrix[6][c] === null) {
                matrix[6][c] =
                    c % 2 === 0;
            }
        }

        // Error correction L = 01, mask pattern = 000.
        const formatBits = bchTypeInfo(
            (1 << 3) | 0
        );

        for (let i = 0; i < 15; i++) {
            const dark =
                ((formatBits >> i) & 1) === 1;

            if (i < 6) {
                matrix[i][8] = dark;
            } else if (i < 8) {
                matrix[i + 1][8] = dark;
            } else {
                matrix[
                    SIZE - 15 + i
                ][8] = dark;
            }
        }

        for (let i = 0; i < 15; i++) {
            const dark =
                ((formatBits >> i) & 1) === 1;

            if (i < 8) {
                matrix[8][
                    SIZE - i - 1
                ] = dark;
            } else if (i < 9) {
                matrix[8][
                    15 - i
                ] = dark;
            } else {
                matrix[8][
                    15 - i - 1
                ] = dark;
            }
        }

        // Fixed dark module.
        matrix[SIZE - 8][8] = true;

        const codewords =
            createCodewords(text);

        let row = SIZE - 1;
        let inc = -1;
        let bitIndex = 7;
        let byteIndex = 0;

        for (
            let originalCol = SIZE - 1;
            originalCol > 0;
            originalCol -= 2
        ) {
            let col = originalCol;

            if (col <= 6) {
                col--;
            }

            while (true) {
                [col, col - 1].forEach(
                    function (c) {
                        if (
                            matrix[row][c] !== null
                        ) {
                            return;
                        }

                        let dark = false;

                        if (
                            byteIndex <
                            codewords.length
                        ) {
                            dark =
                                (
                                    (
                                        codewords[byteIndex] >>
                                        bitIndex
                                    ) & 1
                                ) === 1;
                        }

                        // Mask pattern 0.
                        if (
                            (row + c) % 2 === 0
                        ) {
                            dark = !dark;
                        }

                        matrix[row][c] = dark;

                        bitIndex--;

                        if (bitIndex === -1) {
                            byteIndex++;
                            bitIndex = 7;
                        }
                    }
                );

                row += inc;

                if (
                    row < 0 ||
                    row >= SIZE
                ) {
                    row -= inc;
                    inc = -inc;
                    break;
                }
            }
        }

        return matrix;
    }

    function svgForMatrix(matrix, pixelSize) {
        const quiet = 4;
        const totalModules =
            SIZE + quiet * 2;

        const rects = [];

        for (let r = 0; r < SIZE; r++) {
            for (let c = 0; c < SIZE; c++) {
                if (matrix[r][c]) {
                    rects.push(
                        '<rect x="' +
                        (c + quiet) +
                        '" y="' +
                        (r + quiet) +
                        '" width="1" height="1"/>'
                    );
                }
            }
        }

        return (
            '<svg ' +
            'xmlns="http://www.w3.org/2000/svg" ' +
            'viewBox="0 0 ' +
            totalModules +
            ' ' +
            totalModules +
            '" ' +
            'width="' +
            pixelSize +
            '" ' +
            'height="' +
            pixelSize +
            '" ' +
            'role="img" ' +
            'aria-label="QR Code Nomor Reservasi" ' +
            'shape-rendering="crispEdges">' +
            '<rect width="100%" height="100%" fill="#ffffff"/>' +
            '<g fill="#000000">' +
            rects.join('') +
            '</g>' +
            '</svg>'
        );
    }

    function render(container, text, size) {
        if (!container) {
            throw new Error(
                'Container QR tidak tersedia.'
            );
        }

        const value =
            String(text || '').trim();

        if (!value) {
            container.innerHTML = '';
            return false;
        }

        const matrix =
            createMatrix(value);

        container.innerHTML =
            svgForMatrix(
                matrix,
                Number(size) || 150
            );

        return true;
    }

    global.NadiQRCode = {
        render: render
    };
})(window);
