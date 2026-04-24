describe('Plaudit - Widget show', function () {
    it("Plaudit widget is shown in preprint's page", function () {
        cy.visit('');

        cy.get('a[href*="/preprint/view/"]').first().click();

        cy.get('.item.plaudit').should('exist');
        cy.contains('Plaudit');
    });
});