describe('Plaudit - Widget show', function () {
    const submissionViewLinks = [
        'a[href*="/preprint/view/"]',
        'a[href*="/article/view/"]',
        'a[href*="/catalog/book/"]',
    ].join(', ');

    it("Plaudit widget is shown in submission's page", function () {
        cy.visit('');

        cy.get(submissionViewLinks).first().click();

        cy.get('.item.plaudit').should('exist');
        cy.contains('Plaudit');
    });
});
