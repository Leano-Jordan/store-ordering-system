const globals = require("globals");

module.exports = [
    {
        files: [
            "assets/js/**/*.js",
            "prelaunch/**/*.js",
            "tests/Unit/**/*.js"
        ],
        languageOptions: {
            ecmaVersion: "latest",
            sourceType: "script",
            globals: {
                ...globals.browser,
                ...globals.node,
                $: "readonly",
                jQuery: "readonly",
                Chart: "readonly",
                bootstrap: "readonly",
                Swal: "readonly"
            }
        },
        rules: {
            "no-undef": "warn",
            "no-unused-vars": ["warn", {
                argsIgnorePattern: "^_",
                varsIgnorePattern: "^_"
            }]
        }
    }
];
