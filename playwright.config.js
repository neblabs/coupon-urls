import { defineConfig } from '@playwright/test';

const snapshotTemplate = '{testDir}/{testFileDir}/{testFileName}-snapshots/{arg}{-projectName}{ext}';

const config = defineConfig({
    testDir: './e2e',
    globalSetup: require.resolve('./e2e/config/global-setup.js'),
    fullyParallel: true,
    timeout: 90_000,
    use: {
        baseURL: process.env.WP_BASE_URL || 'http://localhost:8888',
        trace: 'on-first-retry',
        video: 'retain-on-failure',
        channel: 'chrome',
        deviceScaleFactor: 1

    },
    snapshotPathTemplate: snapshotTemplate,
    expect: {
        timeout: 20000, // We need to wait a lil longer for wordpress otherwise some tests will fail
        toHaveScreenshot: {
            // Targets the default snapshot folder structure, but omits the platform token
            pathTemplate: snapshotTemplate,
            maxDiffPixelRatio: 0.10,
            threshold: 0.25,
        },
    },
    projects: [
        {
            name: 'Google Chrome',
            use: {
                channel: 'chrome', // Must be explicitly set inside the project's 'use' block
            },
        },
    ],
});

export default config;
