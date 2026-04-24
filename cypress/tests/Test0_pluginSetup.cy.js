describe('Plaudit - Plugin setup', function () {
    const dummyIntegrationToken = 'dummy_token';

    it('Enables Plaudit plugin and configures it', function () {
        cy.login('dbarnes', null, 'publicknowledge');

        cy.get('nav').contains('Settings').click();
        cy.get('nav').contains('Website').click({ force: true });

        cy.waitJQuery();
        cy.get('#plugins-button').click();

        cy.get('input[id^=select-cell-plauditplugin]').check();
        cy.get('input[id^=select-cell-plauditplugin]').should('be.checked');
        cy.waitJQuery();

        cy.get('tr[id*="plauditplugin"] a.show_extras').click();
        cy.get('a[id*="plauditplugin-settings"]').click();
        cy.get('input[name="integrationToken"]').clear().type(dummyIntegrationToken, { delay: 0 });
        cy.get('#plauditSettingsForm button:contains("Save")').click();
        cy.waitJQuery();
    });
});
