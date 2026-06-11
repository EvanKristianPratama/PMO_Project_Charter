import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            "@": "/resources/js",
        },
    },
    server: {
        hmr: {
            host: "localhost",
        },
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (!id.includes("node_modules")) {
                        return;
                    }

                    if (id.includes("bpmn-js")) {
                        return "bpmn";
                    }

                    if (id.includes("chart.js")) {
                        return "chart";
                    }

                    if (id.includes("sweetalert2")) {
                        return "sweetalert2";
                    }

                    if (id.includes("@vue-flow")) {
                        return "vue-flow";
                    }

                    if (id.includes("html-to-image")) {
                        return "html-to-image";
                    }

                    if (id.includes("jszip")) {
                        return "jszip";
                    }

                    if (id.includes("@inertiajs")) {
                        return "inertia";
                    }

                    if (id.includes("@headlessui")) {
                        return "headlessui";
                    }

                    if (id.includes("@heroicons")) {
                        return "heroicons";
                    }

                    const nodeModulesIndex = id.lastIndexOf("node_modules/");
                    if (nodeModulesIndex === -1) {
                        return;
                    }

                    const packagePath = id.slice(nodeModulesIndex + "node_modules/".length);
                    const parts = packagePath.split("/");
                    const packageName = parts[0].startsWith("@") && parts.length > 1
                        ? `${parts[0]}/${parts[1]}`
                        : parts[0];

                    if (
                        packageName === "vue" ||
                        packageName === "@tanstack/virtual-core" ||
                        packageName === "@tanstack/vue-virtual"
                    ) {
                        return;
                    }

                    return `vendor-${packageName.replace("@", "").replace("/", "-")}`;
                },
            },
        },
    },
});
