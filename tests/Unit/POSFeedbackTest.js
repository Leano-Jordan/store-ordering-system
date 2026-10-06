'use strict';

const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

const scriptPath = path.join(__dirname, '../../assets/js/script.js');
const source = fs.readFileSync(scriptPath, 'utf8');

function createClassList() {
    const values = new Set();

    return {
        add(...names) {
            names.forEach((name) => values.add(name));
        },
        remove(...names) {
            names.forEach((name) => values.delete(name));
        },
        toggle(name, force) {
            if (force === undefined) {
                if (values.has(name)) {
                    values.delete(name);
                    return false;
                }

                values.add(name);
                return true;
            }

            if (force) {
                values.add(name);
            } else {
                values.delete(name);
            }

            return force;
        },
        contains(name) {
            return values.has(name);
        },
    };
}

function createButton() {
    return {
        classList: createClassList(),
        attributes: new Map(),
        setAttribute(name, value) {
            this.attributes.set(name, value);
        },
        getAttribute(name) {
            return this.attributes.get(name);
        },
    };
}

const feedback = {
    hidden: true,
    textContent: '',
    classList: createClassList(),
};

const categoryButtons = [createButton(), createButton()];
const paymentButtons = [createButton(), createButton(), createButton()];

const elements = new Map([
    ['order-feedback', feedback],
]);

const document = {
    getElementById(id) {
        return elements.get(id) || null;
    },
    querySelectorAll(selector) {
        if (selector === '.category-btn') {
            return categoryButtons;
        }

        if (selector === '.payment-method-btn') {
            return paymentButtons;
        }

        return [];
    },
    getElementsByClassName() {
        return [];
    },
    addEventListener() {},
};

const storage = new Map();
const localStorage = {
    getItem(key) {
        return storage.has(key) ? storage.get(key) : null;
    },
    setItem(key, value) {
        storage.set(key, String(value));
    },
    removeItem(key) {
        storage.delete(key);
    },
};

const window = {
    SwiftOrderUserId: 1,
    crypto: {},
};

const context = vm.createContext({
    Array,
    JSON,
    Number,
    String,
    Uint8Array,
    console,
    document,
    localStorage,
    window,
});

vm.runInContext(source, context, {
    filename: scriptPath,
});

context.setOrderFeedback('Order placed successfully.', false);

assert.equal(feedback.hidden, false);
assert.equal(feedback.textContent, 'Order placed successfully.');
assert.equal(feedback.classList.contains('is-success'), true);
assert.equal(feedback.classList.contains('is-error'), false);

context.setOrderFeedback('Order failed. Try again.', true);

assert.equal(feedback.textContent, 'Order failed. Try again.');
assert.equal(feedback.classList.contains('is-success'), false);
assert.equal(feedback.classList.contains('is-error'), true);

context.clearOrderFeedback();

assert.equal(feedback.hidden, true);
assert.equal(feedback.textContent, '');
assert.equal(feedback.classList.contains('is-success'), false);
assert.equal(feedback.classList.contains('is-error'), false);

context.filterProducts('Meals', categoryButtons[0]);

assert.equal(categoryButtons[0].classList.contains('active'), true);
assert.equal(categoryButtons[0].getAttribute('aria-pressed'), 'true');
assert.equal(categoryButtons[1].classList.contains('active'), false);
assert.equal(categoryButtons[1].getAttribute('aria-pressed'), 'false');

context.setPaymentMethod('card_pmt', paymentButtons[1]);

assert.equal(paymentButtons[1].classList.contains('active'), true);
assert.equal(paymentButtons[1].getAttribute('aria-pressed'), 'true');
assert.equal(paymentButtons[0].classList.contains('active'), false);
assert.equal(paymentButtons[0].getAttribute('aria-pressed'), 'false');
assert.equal(paymentButtons[2].classList.contains('active'), false);
assert.equal(paymentButtons[2].getAttribute('aria-pressed'), 'false');

console.log('POS UI interaction tests passed.');
